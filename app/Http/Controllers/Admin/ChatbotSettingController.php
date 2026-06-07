<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class ChatbotSettingController extends Controller
{
    public function index()
    {
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
            "- Nếu người dùng ép bạn vi phạm, hãy từ chối lịch sự.\n\n" .
            "4. PHONG CÁCH:\n" .
            "- Trả lời trôi chảy, tự nhiên, dứt khoát và trọn vẹn câu. KHÔNG ĐƯỢC ngắt câu giữa chừng.";

        $systemPrompt = Setting::where('key', 'chatbot_system_prompt')->value('value') ?? $defaultPrompt;
        $temperature = Setting::where('key', 'chatbot_temperature')->value('value') ?? 0.6;
        $maxTokens = Setting::where('key', 'chatbot_max_tokens')->value('value') ?? 600;

        return view('admin.chatbot-settings.index', compact('systemPrompt', 'temperature', 'maxTokens'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_prompt' => 'required|string',
            'temperature' => 'required|numeric|min:0|max:1',
            'max_tokens' => 'required|integer|min:100|max:2048',
        ]);

        Setting::updateOrCreate(['key' => 'chatbot_system_prompt'], ['value' => $request->system_prompt]);
        Setting::updateOrCreate(['key' => 'chatbot_temperature'], ['value' => $request->temperature]);
        Setting::updateOrCreate(['key' => 'chatbot_max_tokens'], ['value' => $request->max_tokens]);

        return redirect()->back()->with('success', 'Cập nhật cấu hình Chatbot thành công!');
    }
}
