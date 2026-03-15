<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Post;
use App\Models\Category;
use App\Models\Author;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    /**
     * FAQ - Câu trả lời nhanh cho các câu hỏi phổ biến
     */
    private $faqs = [
        'faq_account' => [
            'keywords' => ['đăng ký', 'tạo tài khoản', 'register', 'sign up', 'làm sao để đăng ký'],
            'answer' => 'Để đăng ký tài khoản Góc Sách, bạn click vào nút "Đăng ký" ở góc trên bên phải trang web. Chỉ cần nhập email, tên hiển thị và mật khẩu là xong. Sau khi đăng ký, bạn có thể viết review, bình luận và tạo tủ sách cá nhân.'
        ],
        'faq_login' => [
            'keywords' => ['đăng nhập', 'login', 'quên mật khẩu', 'không vào được'],
            'answer' => 'Để đăng nhập, click vào nút "Đăng nhập" ở góc trên bên phải. Nếu quên mật khẩu, bạn có thể sử dụng chức năng "Quên mật khẩu" để lấy lại qua email đã đăng ký.'
        ],
        'faq_review' => [
            'keywords' => ['viết review', 'đăng review', 'cách review', 'làm sao review'],
            'answer' => 'Để viết review sách, bạn cần đăng nhập trước. Sau đó vào trang chi tiết cuốn sách muốn review và click vào nút "Viết Review". Chia sẻ cảm nhận, đánh giá sao và nội dung review của bạn.'
        ],

        'faq_about' => [
            'keywords' => ['góc sách là gì', 'giới thiệu', 'về góc sách', 'website này'],
            'answer' => 'Góc Sách là cộng đồng yêu sách Việt Nam, nơi bạn có thể khám phá sách hay, đọc và viết review, tham gia thảo luận với những người cùng đam mê đọc sách. Website có hàng nghìn đầu sách với đa dạng thể loại.'
        ],
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Lấy lịch sử chat của user hiện tại
     */
    public function getHistory()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'messages' => []
            ]);
        }

        $messages = ChatMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->limit(50) // Giới hạn 50 tin nhắn gần nhất
            ->get(['role', 'content', 'created_at']);

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Xóa lịch sử chat của user hiện tại
     */
    public function clearHistory()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để xóa lịch sử chat.'
            ], 401);
        }

        ChatMessage::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa lịch sử chat.'
        ]);
    }

    /**
     * Lưu tin nhắn vào database
     */
    private function saveMessage($role, $content)
    {
        if (Auth::check()) {
            ChatMessage::create([
                'user_id' => Auth::id(),
                'role' => $role,
                'content' => $content,
            ]);
        }
    }

    /**
     * Phát hiện intent (ý định) của người dùng
     */
    private function detectIntent($message)
    {
        $message = mb_strtolower($message);
        $intents = [];

        // Greeting - Chào hỏi
        if (preg_match('/(xin chào|hello|hi|chào bạn|chào|hey|alo)/ui', $message)) {
            $intents[] = 'greeting';
        }

        // Farewell - Tạm biệt
        if (preg_match('/(tạm biệt|bye|goodbye|chào nhé|hẹn gặp lại)/ui', $message)) {
            $intents[] = 'farewell';
        }

        // Thanks - Cảm ơn
        if (preg_match('/(cảm ơn|thank|cám ơn|thanks)/ui', $message)) {
            $intents[] = 'thanks';
        }

        // Search book by title
        if (preg_match('/(tìm sách|tìm cuốn|có sách|có cuốn|kiếm sách|search)/ui', $message)) {
            $intents[] = 'search_book';
        }

        // Search by author
        if (preg_match('/(sách của|tác giả|author|viết bởi|của tác giả)/ui', $message)) {
            $intents[] = 'search_author';
        }

        // Category/Genre
        if (preg_match('/(thể loại|category|loại sách|sách về|sách thuộc|genre)/ui', $message)) {
            $intents[] = 'search_category';
        }

        // Recommendation
        if (preg_match('/(gợi ý|đề xuất|recommend|nên đọc|sách hay|đọc gì|hay nhất)/ui', $message)) {
            $intents[] = 'recommend';
        }

        // Statistics
        if (preg_match('/(thống kê|bao nhiêu|tổng số|có mấy|đếm|số lượng)/ui', $message)) {
            $intents[] = 'statistics';
        }

        // Help
        if (preg_match('/(giúp|help|hướng dẫn|làm sao|cách|hỗ trợ)/ui', $message)) {
            $intents[] = 'help';
        }

        // FAQ detection
        foreach ($this->faqs as $faqKey => $faq) {
            foreach ($faq['keywords'] as $keyword) {
                if (mb_strpos($message, $keyword) !== false) {
                    $intents[] = $faqKey;
                    break;
                }
            }
        }

        return empty($intents) ? ['general'] : array_unique($intents);
    }

    /**
     * Lấy câu trả lời FAQ nếu có
     */
    private function getFaqResponse($intents)
    {
        foreach ($intents as $intent) {
            if (isset($this->faqs[$intent])) {
                return $this->faqs[$intent]['answer'];
            }
        }
        return null;
    }

    /**
     * Tìm kiếm thông minh trong database
     */
    private function smartSearch($message, $intents)
    {
        $message = mb_strtolower($message);
        $results = [];

        // Tìm theo tác giả
        if (in_array('search_author', $intents) || preg_match('/(sách|tác phẩm|của|tác giả|author)\s+(.+)/ui', $message, $matches)) {
            $keyword = isset($matches[2]) ? trim($matches[2]) : $this->extractKeyword($message);
            if ($keyword) {
                $books = Book::where('is_approved', true)
                    ->where(function ($q) use ($keyword) {
                        $q->where('author_name', 'like', "%{$keyword}%")
                            ->orWhereHas('author', function ($q) use ($keyword) {
                                $q->where('name', 'like', "%{$keyword}%");
                            });
                    })
                    ->select('title', 'author_name', 'average_rating', 'slug')
                    ->orderByDesc('average_rating')
                    ->limit(10)
                    ->get();

                if ($books->count() > 0) {
                    $results['type'] = 'author_books';
                    $results['keyword'] = $keyword;
                    $results['books'] = $books->toArray();
                }
            }
        }

        // Tìm theo thể loại
        if (in_array('search_category', $intents) || preg_match('/(thể loại|category|loại sách|sách về|sách thuộc)\s+(.+)/ui', $message, $matches)) {
            $keyword = isset($matches[2]) ? trim($matches[2]) : $this->extractKeyword($message);
            if ($keyword && empty($results)) {
                $books = Book::where('is_approved', true)
                    ->whereHas('categories', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })
                    ->select('title', 'author_name', 'average_rating', 'slug')
                    ->orderByDesc('average_rating')
                    ->limit(10)
                    ->get();

                if ($books->count() > 0) {
                    $results['type'] = 'category_books';
                    $results['keyword'] = $keyword;
                    $results['books'] = $books->toArray();
                }
            }
        }

        // Tìm sách theo tên (mở rộng: tìm trong title và description)
        if ((in_array('search_book', $intents) || preg_match('/(tìm sách|sách|tìm|có cuốn|cuốn)\s+(.+)/ui', $message, $matches)) && empty($results)) {
            $keyword = isset($matches[2]) ? trim($matches[2]) : $this->extractKeyword($message);
            if ($keyword) {
                $books = Book::where('is_approved', true)
                    ->where(function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%")
                            ->orWhere('description', 'like', "%{$keyword}%");
                    })
                    ->select('title', 'author_name', 'average_rating', 'slug')
                    ->orderByDesc('average_rating')
                    ->limit(10)
                    ->get();

                if ($books->count() > 0) {
                    $results['type'] = 'search_books';
                    $results['keyword'] = $keyword;
                    $results['books'] = $books->toArray();
                }
            }
        }

        // Đếm số lượng sách
        if (in_array('statistics', $intents) && preg_match('/(bao nhiêu|tổng số|có mấy|đếm)\s*(sách|cuốn)/ui', $message)) {
            $totalBooks = Book::where('is_approved', true)->count();
            $results['type'] = 'count_books';
            $results['total'] = $totalBooks;
        }

        // Thống kê chung
        if (in_array('statistics', $intents) && preg_match('/(thống kê|thong ke|tổng quan|overview)/ui', $message)) {
            $results['type'] = 'statistics';
            $results['stats'] = [
                'total_books' => Book::where('is_approved', true)->count(),
                'total_reviews' => Post::where('status', 'published')->whereNotNull('book_id')->count(),
                'total_categories' => Category::count(),
                'total_authors' => Author::count(),
            ];
        }

        // Sách hay nhất / Gợi ý
        if (in_array('recommend', $intents) && empty($results)) {
            $books = Book::where('is_approved', true)
                ->where('average_rating', '>', 0)
                ->orderByDesc('average_rating')
                ->select('title', 'author_name', 'average_rating', 'slug')
                ->limit(10)
                ->get();

            if ($books->count() > 0) {
                $results['type'] = 'top_books';
                $results['books'] = $books->toArray();
            }
        }

        // Tìm bài review liên quan (mới)
        if (empty($results)) {
            $keyword = $this->extractKeyword($message);
            if ($keyword && strlen($keyword) >= 3) {
                $posts = Post::where('status', 'published')
                    ->where(function ($q) use ($keyword) {
                        $q->where('title', 'like', "%{$keyword}%")
                            ->orWhere('content', 'like', "%{$keyword}%");
                    })
                    ->with('book:id,title,slug')
                    ->limit(5)
                    ->get(['id', 'title', 'book_id']);

                if ($posts->count() > 0) {
                    $results['type'] = 'related_posts';
                    $results['keyword'] = $keyword;
                    $results['posts'] = $posts->toArray();
                }
            }
        }

        return $results;
    }

    /**
     * Trích xuất từ khóa chính từ message
     */
    private function extractKeyword($message)
    {
        // Loại bỏ các từ phổ biến không có ý nghĩa tìm kiếm
        $stopWords = ['tìm', 'sách', 'của', 'có', 'không', 'cho', 'tôi', 'mình', 'bạn', 'xin', 'vui lòng', 'giúp', 'với', 'về', 'là', 'được', 'hay', 'nhất', 'thể loại', 'tác giả'];

        $words = preg_split('/\s+/', mb_strtolower($message));
        $keywords = [];

        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) >= 2 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }

        return implode(' ', array_slice($keywords, 0, 3));
    }

    /**
     * Tạo context từ kết quả tìm kiếm
     */
    private function buildDatabaseContext($searchResults)
    {
        if (empty($searchResults)) {
            return '';
        }

        $context = "\n\n[DỮ LIỆU TỪ DATABASE GÓC SÁCH]\n";

        switch ($searchResults['type']) {
            case 'author_books':
                $context .= "Tìm thấy " . count($searchResults['books']) . " sách của \"{$searchResults['keyword']}\":\n";
                foreach ($searchResults['books'] as $book) {
                    $rating = $book['average_rating'] ? " (Rating: " . number_format($book['average_rating'], 1) . "/5)" : "";
                    $context .= "- {$book['title']} - {$book['author_name']}{$rating}\n";
                }
                break;

            case 'category_books':
                $context .= "Sách thể loại \"{$searchResults['keyword']}\":\n";
                foreach ($searchResults['books'] as $book) {
                    $rating = $book['average_rating'] ? " (Rating: " . number_format($book['average_rating'], 1) . "/5)" : "";
                    $context .= "- {$book['title']} - {$book['author_name']}{$rating}\n";
                }
                break;

            case 'search_books':
                $context .= "Kết quả tìm kiếm \"{$searchResults['keyword']}\":\n";
                foreach ($searchResults['books'] as $book) {
                    $rating = $book['average_rating'] ? " (Rating: " . number_format($book['average_rating'], 1) . "/5)" : "";
                    $context .= "- {$book['title']} - {$book['author_name']}{$rating}\n";
                }
                break;

            case 'count_books':
                $context .= "Tổng số sách trên Góc Sách: {$searchResults['total']} cuốn\n";
                break;

            case 'statistics':
                $stats = $searchResults['stats'];
                $context .= "Thống kê Góc Sách:\n";
                $context .= "- Tổng sách: {$stats['total_books']} cuốn\n";
                $context .= "- Tổng bài review: {$stats['total_reviews']} bài\n";
                $context .= "- Số thể loại: {$stats['total_categories']} loại\n";
                $context .= "- Số tác giả: {$stats['total_authors']} người\n";
                break;

            case 'top_books':
                $context .= "Top sách được đánh giá cao nhất:\n";
                foreach ($searchResults['books'] as $index => $book) {
                    $rank = $index + 1;
                    $context .= "{$rank}. {$book['title']} - {$book['author_name']} (Rating: " . number_format($book['average_rating'], 1) . "/5)\n";
                }
                break;

            case 'related_posts':
                $context .= "Bài review liên quan đến \"{$searchResults['keyword']}\":\n";
                foreach ($searchResults['posts'] as $post) {
                    $bookTitle = $post['book']['title'] ?? 'Bài viết';
                    $context .= "- {$post['title']} (về sách: {$bookTitle})\n";
                }
                break;
        }

        $context .= "\nHãy dựa vào dữ liệu trên để trả lời người dùng một cách chính xác và hữu ích.";

        return $context;
    }

    /**
     * Xử lý các intent đặc biệt và trả về response nhanh
     */
    private function getQuickResponse($intents)
    {
        if (in_array('greeting', $intents) && count($intents) === 1) {
            return 'Xin chào! Tôi là trợ lý AI của Góc Sách. Tôi có thể giúp bạn tìm sách hay, gợi ý sách theo thể loại, hoặc trả lời các câu hỏi về website. Bạn cần gì nào?';
        }

        if (in_array('farewell', $intents)) {
            return 'Tạm biệt bạn! Chúc bạn đọc sách vui vẻ. Hẹn gặp lại!';
        }

        if (in_array('thanks', $intents) && count($intents) === 1) {
            return 'Không có gì! Rất vui được giúp bạn. Nếu cần gì thêm, cứ hỏi nhé!';
        }

        return null;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');

        // Lưu tin nhắn của user vào database
        $this->saveMessage('user', $userMessage);

        // Phát hiện intent
        $intents = $this->detectIntent($userMessage);

        // Kiểm tra quick response (greeting, farewell, thanks)
        $quickResponse = $this->getQuickResponse($intents);
        if ($quickResponse) {
            // Lưu response vào database
            $this->saveMessage('assistant', $quickResponse);
            return response()->json([
                'success' => true,
                'reply' => $quickResponse
            ]);
        }

        // Kiểm tra FAQ
        $faqResponse = $this->getFaqResponse($intents);
        if ($faqResponse) {
            // Lưu response vào database
            $this->saveMessage('assistant', $faqResponse);
            return response()->json([
                'success' => true,
                'reply' => $faqResponse
            ]);
        }

        // Lấy lịch sử chat từ database thay vì từ request
        $history = [];
        if (Auth::check()) {
            $dbMessages = ChatMessage::where('user_id', Auth::id())
                ->orderBy('created_at', 'asc')
                ->limit(20) // Lấy 20 tin nhắn gần nhất cho context
                ->get(['role', 'content']);

            foreach ($dbMessages as $msg) {
                $history[] = [
                    'role' => $msg->role,
                    'content' => $msg->content
                ];
            }
        }

        // Tìm kiếm thông minh trong database
        $searchResults = $this->smartSearch($userMessage, $intents);
        $databaseContext = $this->buildDatabaseContext($searchResults);

        // System prompt cải tiến - KHÔNG CÓ EMOJI
        $systemPrompt = "Bạn là trợ lý AI của website Góc Sách - một cộng đồng yêu sách Việt Nam.

KIẾN THỨC VỀ GÓC SÁCH:
- Website chuyên về sách và review sách tiếng Việt
- Các thể loại phổ biến: Tiểu thuyết, Văn học Việt Nam, Self-help, Kinh doanh, Tâm lý, Light Novel, Manga
- Cho phép người dùng: đăng review, bình luận, đánh giá sách, tạo tủ sách cá nhân
- Các tác giả nổi tiếng Việt Nam: Nguyễn Nhật Ánh, Nguyễn Ngọc Tư, Nam Cao, Ngô Tất Tố

QUY TẮC TRẢ LỜI (RẤT QUAN TRỌNG):
- Trả lời bằng tiếng Việt, thân thiện và tự nhiên như một người bạn yêu sách
- KHÔNG sử dụng emoji trong câu trả lời
- Trả lời ngắn gọn 3-5 câu, đi thẳng vào vấn đề
- Khi gợi ý sách, đề cập tên tác giả và rating nếu có
- Nếu không tìm thấy trong database, nói rõ 'Góc Sách hiện chưa có sách này' và gợi ý sách tương tự nếu có thể
- Khi có dữ liệu từ database, sử dụng chính xác thông tin đó
- Format danh sách sách rõ ràng, dễ đọc

VÍ DỤ TRẢ LỜI TỐT:
User: Có sách của Nguyễn Nhật Ánh không?
Bot: Góc Sách có nhiều sách của Nguyễn Nhật Ánh. Một số tác phẩm nổi bật: Mắt Biếc (Rating 4.8/5), Tôi Thấy Hoa Vàng Trên Cỏ Xanh (Rating 4.7/5), Cho Tôi Xin Một Vé Đi Tuổi Thơ. Bạn muốn tìm cuốn nào cụ thể?" . $databaseContext;

        // Build conversation history
        $contents = [];

        // Add system context as first message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $systemPrompt]]
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => 'Xin chào! Tôi là trợ lý AI của Góc Sách. Tôi có thể giúp bạn tìm sách hay, gợi ý đọc theo sở thích, hoặc trả lời các câu hỏi về website. Bạn cần gì nào?']]
        ];

        // Add conversation history from database
        foreach ($history as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]]
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '?key=' . $this->apiKey, [
                        'contents' => $contents,
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'topK' => 40,
                            'topP' => 0.95,
                            'maxOutputTokens' => 800,
                        ],
                        'safetySettings' => [
                            ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                            ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                            ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                            ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ],
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi không thể trả lời lúc này.';

                // Lưu response của bot vào database
                $this->saveMessage('assistant', $reply);

                return response()->json([
                    'success' => true,
                    'reply' => $reply
                ]);
            } else {
                Log::error('Gemini API Error', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'reply' => 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau!'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'reply' => 'Xin lỗi, không thể kết nối đến AI. Vui lòng thử lại!'
            ], 500);
        }
    }
}
