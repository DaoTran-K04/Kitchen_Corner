<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\ChatbotController;

class TestChatbot extends Command
{
    protected $signature = 'test:chatbot {--q1 : Test Câu 1 (Code)} {--q2 : Test Câu 2 (Nấu cháo)}';
    protected $description = 'Test Chatbot Controller Directly';

    public function handle()
    {
        $this->info("=== BẮT ĐẦU TEST CHATBOT TỪ CODE PROJECT ===");

        $controller = app(ChatbotController::class);

        if ($this->option('q1')) {
            $msg = "Chào bạn, bạn có thể hướng dẫn tôi viết code lập trình một website được không?";
        } elseif ($this->option('q2')) {
            $msg = "Xin chào, hôm nay tôi hơi mệt, bạn có món cháo nào nấu nhanh giúp giải cảm không?";
        } else {
            $this->error("Vui lòng chọn cờ --q1 hoặc --q2 để test.");
            return;
        }

        $this->info("Câu hỏi của User: " . $msg);
        $this->line("Đang gửi qua ChatbotController (có chứa Luật hệ thống)...");

        $request = Request::create('/api/chatbot', 'POST', ['message' => $msg]);
        
        $response = $controller->chat($request);
        $data = json_decode($response->getContent(), true);

        $this->info(">>> KẾT QUẢ AI TRẢ LỜI:");
        if (isset($data['reply']['message'])) {
            $this->line($data['reply']['message']);
        } else {
            $this->error("Lỗi: " . json_encode($data, JSON_UNESCAPED_UNICODE));
        }
        
        $this->info("=== KẾT THÚC TEST ===");
    }
}
