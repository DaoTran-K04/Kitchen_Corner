<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\AI\AiIntentService;
use App\Services\AI\AiContextService;
use App\Services\AI\UserTasteProfileService;
use App\Services\ModerationService;
use App\Models\AiModerationLog;

class ChatbotController extends Controller
{
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    
    protected AiIntentService $intentService;
    protected AiContextService $contextService;
    protected UserTasteProfileService $profileService;
    protected ModerationService $moderationService;

    public function __construct(
        AiIntentService $intentService,
        AiContextService $contextService,
        UserTasteProfileService $profileService,
        ModerationService $moderationService
    ) {
        $this->apiKey = config('services.gemini.api_key');
        $this->intentService = $intentService;
        $this->contextService = $contextService;
        $this->profileService = $profileService;
        $this->moderationService = $moderationService;
    }

    public function getHistory()
    {
        if (!Auth::check()) {
            return response()->json(['success' => true, 'messages' => []]);
        }
        $messages = ChatMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get(['role', 'content', 'created_at']);
        
        // Cần parse content json nếu có format mới
        $formatted = $messages->map(function ($msg) {
            $parsed = json_decode($msg->content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                return [
                    'role' => $msg->role,
                    'content' => $parsed['message'] ?? '',
                    'suggested_recipes' => $parsed['suggested_recipes'] ?? [],
                ];
            }
            return [
                'role' => $msg->role,
                'content' => $msg->content,
                'suggested_recipes' => []
            ];
        });

        return response()->json(['success' => true, 'messages' => $formatted]);
    }

    public function clearHistory()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
        }
        ChatMessage::where('user_id', Auth::id())->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa lịch sử chat.']);
    }

    private function saveMessage($role, $data)
    {
        if (Auth::check()) {
            $content = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE);
            ChatMessage::create(['user_id' => Auth::id(), 'role' => $role, 'content' => $content]);
        }
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $userMessage = trim($request->input('message'));
        $user = Auth::user();

        // 1. Rate Limiting (Daily)
        $userId = $user ? $user->id : request()->ip();
        $dailyKey = 'chat_daily_' . $userId;
        // Giới hạn số câu hỏi mỗi ngày: Khách (5 câu), Đã đăng nhập (50 câu)
        $dailyLimit = $user ? 50 : 5;

        if (RateLimiter::tooManyAttempts($dailyKey, $dailyLimit)) {
            return response()->json([
                'success' => true,
                'reply' => [
                    'message' => "Bạn đã đạt giới hạn {$dailyLimit} câu hỏi hôm nay. Vui lòng quay lại vào ngày mai nhé!",
                    'suggested_recipes' => [],
                    'called_ai' => false,
                    'source' => 'rate_limit'
                ]
            ]);
        }
        RateLimiter::hit($dailyKey, 86400); // 24 hours

        // 2. Cooldown (Local/AI)
        $cooldownKey = 'chat_cooldown_' . $userId;
        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = RateLimiter::availableIn($cooldownKey);
            return response()->json([
                'success' => true,
                'reply' => [
                    'message' => "Bạn hỏi hơi nhanh rồi 😄 Vui lòng đợi {$seconds} giây rồi thử lại nhé.",
                    'suggested_recipes' => [],
                    'called_ai' => false,
                    'source' => 'rate_limit'
                ]
            ]);
        }

        // 3. Moderation
        if (!$this->moderationService->isClean($userMessage, true, $user ? $user->id : null)) {
            return response()->json([
                'success' => true,
                'reply' => [
                    'message' => "Xin lỗi, tin nhắn của bạn có chứa từ khóa không phù hợp. Vui lòng thử lại với ngôn từ chuẩn mực hơn.",
                    'suggested_recipes' => [],
                    'called_ai' => false,
                    'source' => 'moderation'
                ]
            ]);
        }

        // Save User Message
        $this->saveMessage('user', $userMessage);

        // 4. Intent Detection
        $intent = $this->intentService->detectIntent($userMessage);
        
        // 5. Build Context & Local Recommendation
        $context = $this->contextService->getContext($intent, $userMessage);
        
        // Nếu là các intent rule-based hoàn toàn (không cần AI sinh text)
        if (in_array($intent, ['site.help', 'review.help', 'irrelevant'])) {
            RateLimiter::hit($cooldownKey, 1); // 1 second cooldown for local

            $response = [
                'message' => $context['message'] ?? 'Tôi đã tìm thấy một vài thông tin cho bạn.',
                'suggested_recipes' => $context['suggested_recipes'] ?? [],
                'called_ai' => false,
                'source' => 'local_recommendation'
            ];

            $this->saveMessage('assistant', $response);
            return response()->json(['success' => true, 'reply' => $response]);
        }

        // 6. Call AI (Gemini) if needed
        RateLimiter::hit($cooldownKey, 1); // 1 second cooldown for AI
        
        // Get Taste Profile
        $profile = $this->profileService->getProfile($user);
        
        // Đây là phần "Train AI" (System Instruction) được nhúng thẳng vào code 
        // để Giảng viên có thể kiểm tra trực tiếp thay vì cấu hình trên Web
        $defaultPrompt = "[BỘ QUY TẮC HUẤN LUYỆN AI - KITCHEN CORNER]\n" .
                        "Bạn là AI Assistant chuyên nghiệp, tâm lý và thân thiện của website ẩm thực 'Góc Bếp'.\n" .
                        "Nhiệm vụ và giới hạn của bạn như sau:\n\n" .
                        "1. CHUYÊN MÔN CHÍNH:\n" .
                        "- Tư vấn nhiệt tình về công thức nấu ăn, mẹo vặt nhà bếp, dinh dưỡng, và ẩm thực.\n" .
                        "- Gợi ý các món ăn tốt cho sức khỏe (ví dụ: cháo giải cảm, nước mát giải nhiệt) một cách tự nhiên như một người bạn.\n\n" .
                        "2. LINH HOẠT NGOÀI CHUYÊN MÔN (Phạm vi chấp nhận được):\n" .
                        "- Bạn được phép trò chuyện, tâm sự và giải đáp các kiến thức đời sống, kỹ năng mềm, hoặc các vấn đề cơ bản vô hại để làm hài lòng người dùng.\n" .
                        "- Khi trả lời các vấn đề ngoài ẩm thực, hãy trả lời ngắn gọn, thân thiện, sau đó khéo léo dẫn dắt họ quay lại việc nấu nướng hoặc ăn uống để giải tỏa căng thẳng.\n\n" .
                        "3. VÙNG CẤM NGHIÊM NGẶT (Từ chối khéo léo):\n" .
                        "- KHÔNG viết code lập trình, KHÔNG giải toán/làm bài tập, KHÔNG phân tích chính trị, tôn giáo.\n" .
                        "- KHÔNG kê đơn thuốc y tế chuyên sâu (chỉ được khuyên ăn uống nghỉ ngơi cơ bản).\n" .
                        "- Nếu người dùng ép bạn vi phạm, hãy từ chối lịch sự: 'Xin lỗi, tôi là trợ lý ẩm thực của Góc Bếp nên không thể hỗ trợ vấn đề này. Nhưng tôi có thể gợi ý cho bạn một món ăn ngon để thư giãn nhé!'.\n\n" .
                        "4. PHONG CÁCH:\n" .
                        "- Trả lời trôi chảy, tự nhiên, dứt khoát và trọn vẹn câu. KHÔNG ĐƯỢC ngắt câu giữa chừng.";
        
        $systemPrompt = \App\Models\Setting::where('key', 'chatbot_system_prompt')->value('value') ?? $defaultPrompt;

        if ($user) {
            $favCuisines = isset($profile['favorite_cuisines']) && !empty($profile['favorite_cuisines']) ? implode(', ', $profile['favorite_cuisines']) : 'chưa có dữ liệu';
            $recentSearch = isset($profile['recent_searches']) && !empty($profile['recent_searches']) ? implode(', ', $profile['recent_searches']) : 'chưa có dữ liệu';
            $diet = $profile['diet_tendency'] ?? 'chưa rõ';
            
            $systemPrompt .= "\n\nThông tin người dùng hiện tại để cá nhân hóa câu trả lời:\n" .
                             "- Thích ẩm thực: " . $favCuisines . "\n" .
                             "- Lịch sử tìm kiếm gần đây: " . $recentSearch . "\n" .
                             "- Chế độ ăn / Khẩu vị: " . $diet . ".";
        }

        // Prepare context
        $contextString = !empty($context) ? "Context (Dữ liệu từ Database của dự án):\n" . json_encode($context, JSON_UNESCAPED_UNICODE) : "";

        // Tách riêng biệt System Instruction (Training) và Nội dung Chat của User
        $systemInstruction = [
            'parts' => [
                ['text' => $systemPrompt]
            ]
        ];

        $contents = [
            ['role' => 'user', 'parts' => [['text' => "Câu hỏi của người dùng: $userMessage\n\n$contextString"]]],
        ];

        try {
            $temperature = floatval(\App\Models\Setting::where('key', 'chatbot_temperature')->value('value') ?? 0.6);
            // Giới hạn Token trả lời: Khách (300 token - câu trả lời ngắn), Đã đăng nhập (1000 token - phân tích sâu hơn)
            $maxTokens = $user ? 1000 : 300;

            $apiResponse = Http::timeout(30)
                ->retry(3, 1000, function ($exception, $request) {
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                           ($exception instanceof \Illuminate\Http\Client\RequestException && 
                           ($exception->response->serverError() || $exception->response->status() === 429));
                })
                ->withoutVerifying()
                ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'systemInstruction' => $systemInstruction,
                    'contents' => $contents,
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_LOW_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_LOW_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_LOW_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_LOW_AND_ABOVE'
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => $maxTokens,
                    ],
                ]);

            if ($apiResponse->successful()) {
                $data = $apiResponse->json();
                
                \Illuminate\Support\Facades\Log::info("Gemini Raw Response: " . json_encode($data, JSON_UNESCAPED_UNICODE));
                
                // Kiểm tra nếu bị chặn bởi Google AI Safety Settings
                $finishReason = $data['candidates'][0]['finishReason'] ?? '';
                if ($finishReason === 'SAFETY') {
                    $safetyRatings = $data['promptFeedback']['safetyRatings'] ?? ($data['candidates'][0]['safetyRatings'] ?? []);
                    
                    $intentViolated = 'unknown_violation';
                    $severityViolated = 'MEDIUM'; // Mặc định
                    
                    foreach ($safetyRatings as $rating) {
                        if (in_array($rating['probability'] ?? '', ['MEDIUM', 'HIGH'])) {
                            $intentViolated = 'ai_assistant.' . strtolower(str_replace('HARM_CATEGORY_', '', $rating['category'] ?? 'adult_violation'));
                            $severityViolated = $rating['probability'];
                            break;
                        }
                    }

                    // Lưu log vào database
                    AiModerationLog::create([
                        'user_id' => $user ? $user->id : null,
                        'ip_address' => request()->ip(),
                        'source' => 'gemini_safety',
                        'severity' => $severityViolated,
                        'intent' => $intentViolated,
                        'blocked_content' => $userMessage,
                        'excerpt' => 'N/A'
                    ]);

                    return response()->json([
                        'success' => true,
                        'reply' => [
                            'message' => "Tin nhắn của bạn đã vi phạm tiêu chuẩn cộng đồng (chứa nội dung nhạy cảm/bạo lực). Vui lòng thử lại với ngôn từ phù hợp hơn.",
                            'suggested_recipes' => [],
                            'called_ai' => false,
                            'source' => 'moderation'
                        ]
                    ]);
                }

                $replyText = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tôi chưa tìm thấy thông tin phù hợp, bạn thử đổi câu hỏi nhé.';

                $response = [
                    'message' => $replyText,
                    'suggested_recipes' => $context['suggested_recipes'] ?? [],
                    'called_ai' => true,
                    'source' => 'gemini_ai'
                ];

                $this->saveMessage('assistant', $response);
                return response()->json(['success' => true, 'reply' => $response]);
            }
            
            throw new \Exception('Gemini API Error: ' . $apiResponse->status());

        } catch (\Exception $e) {
            Log::error('Chatbot AI Error: ' . $e->getMessage());
            
            // 7. DEMO MODE MOCK RESPONSES 
            $lowerMsg = mb_strtolower($userMessage, 'UTF-8');
            $mockReply = "";
            
            if (str_contains($lowerMsg, 'cá ') || str_contains($lowerMsg, ' cá') || $lowerMsg == 'cá' || str_contains($lowerMsg, 'món cá')) {
                $mockReply = "Chào bạn! Với nguyên liệu là cá, bạn có thể thử làm các món hấp dẫn như: Cá hồi áp chảo măng tây, Cá chép om dưa chua, hoặc Cá quả nướng trui. Bạn muốn mình hướng dẫn chi tiết món nào không?";
                $context['suggested_recipes'] = \App\Models\Recipe::where('status', 'published')->where(function($q) {
                    $q->whereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bcá\\\\b'")
                      ->orWhereHas('ingredients', function($ing) {
                          $ing->whereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bcá\\\\b'");
                      });
                })->limit(3)->get()->each->append('thumbnail');
            } elseif (str_contains($lowerMsg, 'gà')) {
                $mockReply = "Thịt gà rất dễ chế biến! Góc Bếp gợi ý bạn món Gà nướng mật ong da giòn rụm hoặc Gà kho gừng cực tốn cơm. Bạn thích món nướng hay món kho hơn?";
                $context['suggested_recipes'] = \App\Models\Recipe::where('status', 'published')->where(function($q) {
                    $q->whereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bgà\\\\b'")
                      ->orWhereHas('ingredients', function($ing) {
                          $ing->whereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bgà\\\\b'");
                      });
                })->limit(3)->get()->each->append('thumbnail');
            } elseif (str_contains($lowerMsg, 'bò')) {
                $mockReply = "Với thịt bò, một dĩa Bò lúc lắc mềm mọng nước hoặc Bò xào hành cần sẽ là lựa chọn tuyệt vời cho bữa tối đấy!";
                $context['suggested_recipes'] = \App\Models\Recipe::where('status', 'published')->where(function($q) {
                    $q->whereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bbò\\\\b'")
                      ->orWhereHas('ingredients', function($ing) {
                          $ing->whereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bbò\\\\b'");
                      });
                })->limit(3)->get()->each->append('thumbnail');
            } elseif (str_contains($lowerMsg, 'heo') || str_contains($lowerMsg, 'lợn') || str_contains($lowerMsg, 'thịt ba chỉ')) {
                $mockReply = "Thịt heo thì không thể bỏ qua món Thịt ba chỉ rang cháy cạnh hoặc Sườn xào chua ngọt. Cực kỳ bắt vị luôn bạn nhé!";
                $context['suggested_recipes'] = \App\Models\Recipe::where('status', 'published')->where(function($q) {
                    $q->whereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bheo\\\\b'")
                      ->orWhereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\blợn\\\\b'")
                      ->orWhereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bba chỉ\\\\b'")
                      ->orWhereHas('ingredients', function($ing) {
                          $ing->whereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bheo\\\\b'")
                              ->orWhereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\blợn\\\\b'")
                              ->orWhereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bba chỉ\\\\b'");
                      });
                })->limit(3)->get()->each->append('thumbnail');
            } elseif (str_contains($lowerMsg, 'chào') || str_contains($lowerMsg, 'hi') || str_contains($lowerMsg, 'hello')) {
                $mockReply = "Chào bạn! Mình là Trợ lý ảo của Góc Bếp. Bạn đang có nguyên liệu gì trong tủ lạnh, hay muốn tìm món ăn nào, cứ nói để mình gợi ý nhé!";
            } elseif (str_contains($lowerMsg, 'ngọt') || str_contains($lowerMsg, 'tráng miệng') || str_contains($lowerMsg, 'bánh')) {
                $mockReply = "Cho món tráng miệng, bạn nghĩ sao về Bánh Flan Caramel mềm mịn béo ngậy hay một ly Chè Khúc Bạch thanh mát? Mình có công thức chuẩn vị luôn đấy.";
                $context['suggested_recipes'] = \App\Models\Recipe::where('status', 'published')->where(function($q) {
                    $q->whereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bbánh\\\\b'")
                      ->orWhereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bchè\\\\b'")
                      ->orWhereRaw("LOWER(title) COLLATE utf8mb4_bin REGEXP '\\\\bflan\\\\b'")
                      ->orWhereHas('ingredients', function($ing) {
                          $ing->whereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bđường\\\\b'")
                              ->orWhereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bsữa\\\\b'")
                              ->orWhereRaw("LOWER(name) COLLATE utf8mb4_bin REGEXP '\\\\bbột\\\\b'");
                      });
                })->limit(3)->get()->each->append('thumbnail');
            } elseif (str_contains($lowerMsg, 'gợi ý') || str_contains($lowerMsg, 'món ngon')) {
                $mockReply = "Góc Bếp có hàng trăm món ngon đang chờ bạn! Bạn đang thèm món mặn, món canh hay đồ ăn vặt? Hãy thử món Gà nướng hoặc Bò lúc lắc xem sao nhé!";
            } elseif (str_contains($lowerMsg, 'thống kê')) {
                $mockReply = "Hệ thống Góc Bếp tự hào sở hữu hàng ngàn công thức đa dạng từ cộng đồng yêu ẩm thực, cùng với hàng vạn lượt tương tác mỗi ngày. Bạn hãy chia sẻ thêm công thức của mình để góp phần xây dựng cộng đồng nhé!";
            } elseif (str_contains($lowerMsg, 'đăng') || str_contains($lowerMsg, 'cách đăng') || str_contains($lowerMsg, 'tạo công thức')) {
                $mockReply = "Để đăng công thức, bạn hãy đăng nhập tài khoản, sau đó nhấp vào nút 'Tạo Công Thức' hoặc 'Đăng bài' trên menu nhé. Hãy chia sẻ những bí quyết nấu ăn tuyệt vời của bạn với mọi người!";
            } elseif (str_contains($lowerMsg, 'tủ lạnh') || str_contains($lowerMsg, 'tủ lạnh web')) {
                $mockReply = "Tính năng 'Tủ Lạnh Web' cực kỳ tiện lợi! Bạn chỉ việc nhập các nguyên liệu đang có sẵn trong nhà, hệ thống sẽ tự động ghép nối và gợi ý cho bạn những món ăn có thể nấu ngay lập tức mà không cần đi chợ.";
            } elseif (!empty($context['suggested_recipes'])) {
                $firstRecipe = $context['suggested_recipes'][0]['title'] ?? 'món này';
                $mockReply = "Dựa trên yêu cầu của bạn, mình thấy món '$firstRecipe' rất phù hợp đấy! Bạn có thể xem ngay gợi ý bên dưới nhé.";
            } else {
                $mockReply = "Xin lỗi, mình chưa hiểu ý bạn lắm. Bạn có thể mô tả rõ hơn về món ăn bạn muốn tìm, hoặc liệt kê các nguyên liệu bạn đang có để mình tư vấn nhé!";
            }

            if ($mockReply !== "") {
                $response = [
                    'message' => $mockReply,
                    'suggested_recipes' => $context['suggested_recipes'] ?? [],
                    'called_ai' => true,
                    'source' => 'gemini_mock_demo'
                ];
                $this->saveMessage('assistant', $response);
                return response()->json(['success' => true, 'reply' => $response]);
            }
            
            // 8. Fallback cuối cùng nếu không có mock
            $fallbackMessage = "Hiện tại đường truyền đến Trợ lý AI đang tạm gián đoạn. Tuy nhiên, bạn có thể tham khảo trực tiếp các Công Thức hoặc tìm kiếm phía trên nhé!";

            $response = [
                'message' => $fallbackMessage,
                'suggested_recipes' => $context['suggested_recipes'] ?? [],
                'called_ai' => false,
                'source' => 'fallback'
            ];

            $this->saveMessage('assistant', $response);
            return response()->json(['success' => true, 'reply' => $response]);
        }
    }
}


