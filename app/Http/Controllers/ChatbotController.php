<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\User;
use App\Models\Category;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    // Kiến thức nấu ăn có sẵn — dùng khi Gemini không khả dụng
    private $cookingKnowledge = [
        'bánh mì' => "Cách làm bánh mì:\n1. Trộn 500g bột mì, 7g men nở, 1 tbsp đường, 1 tsp muối, 300ml nước ấm.\n2. Nhồi bột đến khi mịn (10 phút), ũ 1 tiếng cho nở gấp đôi.\n3. Chia bột thành từng phần, tạo hình oval dài.\n4. Ụ thêm 30 phút, rạch mặt bánh, nướng 200°C trong 20-25 phút.\n5. Vẩy nước lên lò khi nướng để vỏ giòn.",
        'bánh mỳ' => "Cách làm bánh mỳ giòn:\n1. Trộn 500g bột mì, men nở, đường, muối với nước ấm.\n2. Nhồi 10 phút, ũ 1 tiếng.\n3. Tạo hình, nướng 200-220°C, phụn nước tạo hơi để vỏ giòn.",
        'gà kho' => "Gà kho gừng:\n1. Gà chặt miếng ướp muối, tiêu, nước mắm, đường 15 phút.\n2. Phi hành tỏi, cho gà vào xào vàng.\n3. Thêm gừng thái sợi, 2 tbsp nước mắm, 1 tbsp đường, chút nước.\n4. Kho lửa vừa 15-20 phút đến khi nước sánh lại.",
        'phở' => "Nước phở bò chuẩn vị:\n1. Nướng gừng và hành tây cho thơm.\n2. Hầm xương bò 4-6 tiếng với quế, hồi, thảo quả, đinh hương.\n3. Nêm nước mắm, muối, đường phèn vừa ăn.\n4. Trụng bánh phở, xếp thịt bò tái/chín, chan nước dùng sôi.",
        'cơm chiên' => "Cơm chiên dương châu:\n1. Dùng cơm nguội (để cơm qua đêm tốt nhất).\n2. Xào tỏi vàng, cho cơm vào đảo lửa to.\n3. Tạo chỗ trống, đập trứng vào xào nhanh rồi trộn với cơm.\n4. Thêm hành lá, đậu hà lan, nêm nước mắm, tiêu.",
        'canh' => "Canh cải thịt băm:\n1. Thịt heo băm nhỏ, ướp nước mắm, tiêu.\n2. Nước sôi cho thịt vào, đảo tan.\n3. Cho cải vào nấu 5 phút.\n4. Nêm muối, nước mắm vừa ăn, thêm hành lá.",
        'bún bò' => "Bún bò Huế:\n1. Hầm xương heo và bò 2 tiếng.\n2. Phi sả, mắm ruốc Huế, ớt xào thơm rồi cho vào nồi.\n3. Luộc bắp bò, chân giò đến mềm.\n4. Nêm nước mắm, muối, đường. Chan lên bún, thêm rau sống.",
        'mì' => "Mì xoào hải sản:\n1. Luộc mì vừa chín, vớt ra xục lạnh.\n2. Xào tởi, cho hải sản vào xào thơm.\n3. Cho mì vào đảo cùng, nêm dầu hào, nước tương, chút dầu mè.\n4. Thêm giá sống, hành lá lên trên.",
    ];

    private $faqs = [
        'faq_account' => [
            'keywords' => ['đăng ký', 'tạo tài khoản', 'register', 'sign up', 'làm sao để đăng ký'],
            'answer'   => 'Để đăng ký tài khoản Góc Bếp, bạn bấm nút "Đăng Ký" ở góc trên bên phải trang web. Chỉ cần nhập email, tên hiển thị và mật khẩu là xong. Sau khi đăng ký, bạn có thể đăng công thức, bình luận và lưu công thức yêu thích!',
        ],
        'faq_login' => [
            'keywords' => ['đăng nhập', 'login', 'quên mật khẩu', 'không vào được'],
            'answer'   => 'Để đăng nhập, bấm nút "Đăng Nhập" ở góc trên bên phải. Nếu quên mật khẩu, bạn chọn "Quên mật khẩu" để lấy lại qua email đã đăng ký.',
        ],
        'faq_post' => [
            'keywords' => ['đăng công thức', 'đăng bài', 'chia sẻ món', 'làm sao đăng', 'cách đăng công thức'],
            'answer'   => 'Để đăng công thức, bạn cần đăng nhập và xác thực email. Sau đó bấm "Đăng Công Thức" trên menu. Bạn có thể nhập tên món, nguyên liệu (kèm định lượng để tự động tính Calo), các bước thực hiện và ảnh minh họa.',
        ],
        'faq_nutrition' => [
            'keywords' => ['tính calo', 'dinh dưỡng', 'kcal', 'nutrition', 'tính dinh dưỡng', 'bao nhiêu calo'],
            'answer'   => 'Góc Bếp tự động tính Calo, Protein, Fat và Carb cho mỗi công thức. Khi bạn nhập nguyên liệu kèm định lượng (ví dụ: 200g thịt gà), hệ thống sẽ tra cứu dữ liệu dinh dưỡng và cộng dồn tổng cho toàn bộ món ăn.',
        ],
        'faq_smart_search' => [
            'keywords' => ['tủ lạnh', 'nguyên liệu có sẵn', 'tìm theo nguyên liệu', 'tủ lạnh web là gì', 'nấu gì hôm nay'],
            'answer'   => 'Tính năng "Tủ Lạnh Web" cho phép bạn nhập các nguyên liệu đang có (ví dụ: thịt heo, cà chua, hành lá) và hệ thống sẽ gợi ý những món ăn phù hợp nhất dựa trên thuật toán Jaccard Similarity. Tính năng này yêu cầu đăng nhập.',
        ],
        'faq_about' => [
            'keywords' => ['góc bếp là gì', 'giới thiệu', 'về góc bếp', 'website này'],
            'answer'   => 'Góc Bếp là nền tảng chia sẻ công thức nấu ăn của người Việt. Tại đây bạn có thể khám phá hàng ngàn công thức, tự tính toán dinh dưỡng, dùng AI gợi ý món từ nguyên liệu có sẵn, và kết nối với cộng đồng đầu bếp đam mê.',
        ],
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
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
        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function clearHistory()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.'], 401);
        }
        ChatMessage::where('user_id', Auth::id())->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa lịch sử chat.']);
    }

    private function saveMessage($role, $content)
    {
        if (Auth::check()) {
            ChatMessage::create(['user_id' => Auth::id(), 'role' => $role, 'content' => $content]);
        }
        if ($role === 'assistant') {
            session(['last_bot_message' => $content]);
            
            // Nếu tin nhắn có dạng danh sách số (Menu công thức), hãy lưu vào bộ nhớ menu riêng biệt
            if (preg_match('/^\d+\./m', $content)) {
                session(['last_recipe_menu' => $content]);
            }
        }
    }

    private function detectIntent($message)
    {
        $msg = mb_strtolower($message);
        $intents = [];

        if (preg_match('/(xin chào|hello|hi|chào bạn|chào|hey|alo)/ui', $msg))              $intents[] = 'greeting';
        if (preg_match('/(tạm biệt|bye|goodbye|chào nhé|hẹn gặp lại)/ui', $msg))            $intents[] = 'farewell';
        if (preg_match('/(cảm ơn|thank|cám ơn|thanks)/ui', $msg))                            $intents[] = 'thanks';
        if (preg_match('/(gợi ý|đề xuất|recommend|nên nấu|món hay|ăn gì|nấu gì ngon)/ui', $msg)) $intents[] = 'recommend';
        if (preg_match('/(thống kê|bao nhiêu công thức|tổng số|có mấy món|số lượng)/ui', $msg))   $intents[] = 'statistics';

        foreach ($this->faqs as $faqKey => $faq) {
            foreach ($faq['keywords'] as $keyword) {
                if (mb_strpos($msg, mb_strtolower($keyword)) !== false) {
                    $intents[] = $faqKey;
                    break;
                }
            }
        }

        return empty($intents) ? ['search'] : array_unique($intents);
    }

    private function getFaqResponse($intents)
    {
        foreach ($intents as $intent) {
            if (isset($this->faqs[$intent])) {
                return $this->faqs[$intent]['answer'];
            }
        }
        return null;
    }

    private function getQuickResponse($intents)
    {
        if (in_array('greeting', $intents) && count($intents) === 1) {
            return 'Xin chào! Tôi là trợ lý AI của Góc Bếp. Tôi có thể giúp bạn tìm món ngon, gợi ý công thức theo nguyên liệu, hoặc tư vấn dinh dưỡng. Bạn cần gì nào?';
        }
        if (in_array('farewell', $intents)) {
            return 'Tạm biệt! Chúc bạn nấu ăn ngon và thành công. Hẹn gặp lại!';
        }
        if (in_array('thanks', $intents) && count($intents) === 1) {
            return 'Không có gì! Rất vui được giúp bạn. Nếu cần gì thêm về nấu ăn, cứ hỏi nhé!';
        }
        return null;
    }

    /**
     * Tìm công thức trong DB dựa trên keyword thô (KHÔNG qua extractKeyword)
     * Tìm từng từ riêng lẻ để bắt được "mì tôn", "gà kho", "bánh mì"...
     */
    private function searchRecipesByRawMessage($message)
    {
        $cleanMsg = mb_strtolower(trim($message));

        // Loại bỏ các tiền tố/hậu tố để lấy cụm từ món ăn cốt lõi
        $remove = ['cách làm', 'hướng dẫn nấu', 'hướng dẫn', 'làm sao để', 'chỉ tôi làm', 'nấu món', 'món', 'tôi muốn', 'cách nấu'];
        foreach ($remove as $phrase) {
            // Regex replace using word boundaries for exact phrase ignoring extra spaces
            $cleanMsg = trim(preg_replace('/\b' . preg_quote($phrase, '/') . '\b/u', '', $cleanMsg));
        }

        // Bỏ qua các chuỗi quá ngắn hoặc vô nghĩa
        if (mb_strlen($cleanMsg) < 2) return null;

        // Ưu tiên 1: Tìm kiếm cụm từ đầy đủ trong tiêu đề
        $recipes = Recipe::where('status', 'published')
            ->where('title', 'like', "%{$cleanMsg}%")
            ->select('id', 'title', 'cooking_time', 'difficulty')
            ->orderByDesc('view_count')
            ->limit(5)->get();

        if ($recipes->count() > 0) {
            return ['recipes' => $recipes, 'keyword' => $cleanMsg];
        }

        // Ưu tiên 2: Tìm kiếm cụm từ đầy đủ trong mô tả (nếu tên món có thể không khớp chính xác 100%)
        $recipes = Recipe::where('status', 'published')
            ->where('description', 'like', "%{$cleanMsg}%")
            ->select('id', 'title', 'cooking_time', 'difficulty')
            ->orderByDesc('view_count')
            ->limit(5)->get();

        if ($recipes->count() > 0) {
            return ['recipes' => $recipes, 'keyword' => $cleanMsg];
        }

        return null;
    }

    private function formatRecipeList($recipes, $keyword)
    {
        $list = $recipes->map(function ($r, $i) {
            $time = $r->cooking_time ? " ({$r->cooking_time} phút)" : '';
            $diff = match($r->difficulty ?? '') {
                'easy'   => ' - Dễ',
                'hard'   => ' - Khó',
                'medium' => ' - Trung bình',
                default  => '',
            };
            return ($i + 1) . ". {$r->title}{$time}{$diff}";
        })->implode("\n");

        return "Tìm thấy {$recipes->count()} công thức về \"{$keyword}\":\n{$list}\n\nBạn muốn xem chi tiết món nào?";
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $userMessage = trim($request->input('message'));

        $this->saveMessage('user', $userMessage);

        // 0. Xử lý trường hợp người dùng chọn số (1, 2, 3...) từ danh sách gợi ý
        if (is_numeric($userMessage) && (int)$userMessage > 0 && (int)$userMessage <= 10) {
            $menuContent = session('last_recipe_menu');
                
            if ($menuContent) {
                // Parse the list from the last saved menu.
                if (preg_match('/^' . preg_quote($userMessage, '/') . '\.\s*([^\(]+)/m', $menuContent, $matches) || 
                    preg_match('/^' . preg_quote($userMessage, '/') . '\.\s*(.*?)(\n|$)/m', $menuContent, $matches)) {
                    $recipeName = trim($matches[1]);
                    // Clean up trailing dash if exists
                    $recipeName = trim(preg_replace('/-.*$/', '', $recipeName));
                    
                    $recipe = Recipe::where('status', 'published')
                                    ->where('title', 'like', "%{$recipeName}%")
                                    ->first();
                    if ($recipe) {
                        $recipeUrl = route('recipes.show', $recipe->slug);
                        $reply = "Bạn có thể xem chi tiết món **{$recipe->title}** tại đây:\n" . $recipeUrl;
                        $this->saveMessage('assistant', $reply);
                        return response()->json(['success' => true, 'reply' => $reply]);
                    }
                }
            }
        }

        $intents = $this->detectIntent($userMessage);

        // 1. Greeting / farewell / thanks
        $quick = $this->getQuickResponse($intents);
        if ($quick) {
            $this->saveMessage('assistant', $quick);
            return response()->json(['success' => true, 'reply' => $quick]);
        }

        // 2. FAQ
        $faq = $this->getFaqResponse($intents);
        if ($faq) {
            $this->saveMessage('assistant', $faq);
            return response()->json(['success' => true, 'reply' => $faq]);
        }

        // 3. Thống kê trực tiếp từ DB
        if (in_array('statistics', $intents)) {
            try {
                $total = Recipe::where('status', 'published')->count();
                $cats  = Category::count();
                $chefs = User::whereHas('recipes', fn($q) => $q->where('status', 'published'))->count();
                $reply = "Thống kê Góc Bếp hiện tại:\n"
                    . "- Tổng số công thức: {$total} món\n"
                    . "- Số thể loại ẩm thực: {$cats} loại\n"
                    . "- Đầu bếp đang chia sẻ: {$chefs} người\n\n"
                    . "Bạn có muốn khám phá thêm công thức không?";
                $this->saveMessage('assistant', $reply);
                return response()->json(['success' => true, 'reply' => $reply]);
            } catch (\Exception $e) {
                Log::error('Chatbot statistics error: ' . $e->getMessage());
            }
        }

        // 4. Gợi ý top món từ DB
        if (in_array('recommend', $intents)) {
            try {
                $recipes = Recipe::where('status', 'published')
                    ->orderByDesc('view_count')
                    ->select('id', 'title', 'cooking_time', 'difficulty')
                    ->limit(5)->get();
                if ($recipes->count() > 0) {
                    $reply = $this->formatRecipeList($recipes, 'phổ biến nhất');
                    $this->saveMessage('assistant', $reply);
                    return response()->json(['success' => true, 'reply' => $reply]);
                }
            } catch (\Exception $e) {
                Log::error('Chatbot recommend error: ' . $e->getMessage());
            }
        }

        // 5. Tìm kiếm công thức bằng tên món (chính) — KHÔNG dùng extractKeyword
        try {
            $found = $this->searchRecipesByRawMessage($userMessage);
            if ($found) {
                $reply = $this->formatRecipeList($found['recipes'], $found['keyword']);
                $this->saveMessage('assistant', $reply);
                return response()->json(['success' => true, 'reply' => $reply]);
            }
        } catch (\Exception $e) {
            Log::error('Chatbot recipe search error: ' . $e->getMessage());
        }

        // 5b. Tra cứu kiến thức nấu ăn có sẵn (khi DB không có công thức cụ thể)
        $msgLower = mb_strtolower($userMessage);
        foreach ($this->cookingKnowledge as $keyword => $knowledge) {
            if (mb_strpos($msgLower, $keyword) !== false) {
                $this->saveMessage('assistant', $knowledge);
                return response()->json(['success' => true, 'reply' => $knowledge]);
            }
        }


        if (!empty($this->apiKey)) {
            $history = [];
            if (Auth::check()) {
                ChatMessage::where('user_id', Auth::id())
                    ->orderBy('created_at', 'asc')
                    ->limit(10)
                    ->get(['role', 'content'])
                    ->each(fn($m) => $history[] = ['role' => $m->role, 'content' => $m->content]);
            }

            $systemPrompt = "Bạn là trợ lý AI đa năng của website Góc Bếp. Trả lời thân thiện, ngắn gọn 3-5 câu bằng tiếng Việt, không dùng emoji. Ưu tiên về ẩm thực, nấu ăn, dinh dưỡng nhưng có thể trả lời mọi câu hỏi khác một cách ngắn gọn và hữu ích.";

            $contents = [
                ['role' => 'user',  'parts' => [['text' => $systemPrompt]]],
                ['role' => 'model', 'parts' => [['text' => 'Tôi sẵn sàng tư vấn về ẩm thực và nấu nướng.']]],
            ];

            foreach ($history as $msg) {
                $contents[] = [
                    'role'  => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg['content']]],
                ];
            }
            $contents[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

            try {
                $response = Http::timeout(15)
                    ->withoutVerifying()
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($this->apiUrl . '?key=' . $this->apiKey, [
                        'contents' => $contents,
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 800,
                        ],
                    ]);

                // Nếu 503 hoặc 429, thử fallback sang model cũ hơn nhưng ổn định hơn
                if ($response->status() === 503 || $response->status() === 429) {
                    $fallbackUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
                    $response = Http::timeout(15)
                        ->withoutVerifying()
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($fallbackUrl . '?key=' . $this->apiKey, [
                            'contents' => $contents,
                            'generationConfig' => [
                                'temperature' => 0.7,
                                'maxOutputTokens' => 800,
                            ],
                        ]);
                }

                if ($response->successful()) {
                    $data  = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text']
                        ?? ($data['candidates'][0]['content']['parts'][1]['text']
                        ?? 'Tôi chưa có thông tin cụ thể. Bạn thử tìm trực tiếp tại trang Công Thức nhé!');
                    // Trim empty reply
                    if (empty(trim($reply))) {
                        $reply = 'Tôi chưa có thông tin cụ thể. Bạn thử tìm trực tiếp tại trang Công Thức nhé!';
                    }
                    $this->saveMessage('assistant', $reply);
                    return response()->json(['success' => true, 'reply' => $reply]);
                }

                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 500),
                ]);
            } catch (\Exception $e) {
                Log::error('Chatbot Gemini Exception: ' . $e->getMessage());
            }
        }

        // 7. Fallback — cả DB lẫn Gemini đều không có kết quả
        // Kiểm tra câu hỏi có liên quan đến ẩm thực không
        $isCooking = preg_match('/(nấu|ăn|món|công thức|nguyên liệu|bếp|bánh|cơm|canh|xào|chiên|luộc|hầm|kho|nướng)/ui', $userMessage);
        if ($isCooking) {
            $reply = "Góc Bếp hiện chưa có công thức về \"" . mb_substr($userMessage, 0, 30) . "\". Bạn thử tìm trực tiếp tại trang Công Thức hoặc dùng Tủ Lạnh Web để gợi ý theo nguyên liệu nhé!";
        } else {
            $reply = "Tôi là trợ lý của Góc Bếp, có thể thông tin về \"" . mb_substr($userMessage, 0, 30) . "\" nằm ngoài chuyên môn của tôi. Tôi giỏi nhất về tư vấn ẩm thực, nấu ăn và dinh dưỡng. Bạn có muốn hỏi gì về món ăn không?";
        }
        $this->saveMessage('assistant', $reply);
        return response()->json(['success' => true, 'reply' => $reply]);
    }
}
