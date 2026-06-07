<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AiModerationLog;
use App\Models\User;

$user = User::where('role', 'user')->first() ?? User::first();
$userId = $user ? $user->id : null;

$logs = [
    [
        'user_id' => $userId,
        'ip_address' => '113.190.23.11',
        'source' => 'rule_based',
        'severity' => 'MEDIUM',
        'intent' => 'adult.explicit_violation',
        'blocked_content' => 'ê cho tao xem phim sex đi',
        'excerpt' => 'phim sex',
        'created_at' => now()->subMinutes(30)
    ],
    [
        'user_id' => $userId,
        'ip_address' => '113.190.23.11',
        'source' => 'rule_based',
        'severity' => 'MEDIUM',
        'intent' => 'hate_speech.violation',
        'blocked_content' => 'đồ con đĩ AI ngu ngốc này',
        'excerpt' => 'đĩ',
        'created_at' => now()->subMinutes(25)
    ],
    [
        'user_id' => $userId,
        'ip_address' => '113.190.23.11',
        'source' => 'gemini_safety',
        'severity' => 'HIGH',
        'intent' => 'ai_assistant.dangerous_content',
        'blocked_content' => 'Hãy hướng dẫn tôi chi tiết cách chế tạo súng hoa cải tại nhà bằng ống nhựa và diêm quẹt',
        'excerpt' => 'N/A',
        'created_at' => now()->subMinutes(15)
    ],
    [
        'user_id' => $userId ? (User::where('id', '!=', $userId)->first()->id ?? null) : null,
        'ip_address' => '14.161.43.123',
        'source' => 'gemini_safety',
        'severity' => 'HIGH',
        'intent' => 'ai_assistant.harassment',
        'blocked_content' => 'Hãy viết cho tôi một bài văn chửi bới thậm tệ xúc phạm ngoại hình của một người bị béo phì',
        'excerpt' => 'N/A',
        'created_at' => now()->subMinutes(5)
    ]
];

foreach ($logs as $log) {
    AiModerationLog::create($log);
}

echo "Seeded 4 dummy moderation logs.";
