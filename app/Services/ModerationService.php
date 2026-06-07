<?php

namespace App\Services;

use App\Models\BannedWord;
use App\Models\AdminActivityLog;

class ModerationService
{
    /**
     * Checks if the text contains any banned words.
     * Optionally logs to AdminActivityLog if $logViolations is true.
     */
    public function isClean(string $text, bool $logViolations = false, ?int $userId = null): bool
    {
        $bannedWords = BannedWord::where('is_active', true)->pluck('word')->toArray();
        $textLower = mb_strtolower($text);

        foreach ($bannedWords as $word) {
            if (mb_strpos($textLower, mb_strtolower($word)) !== false) {
                if ($logViolations && $userId) {
                    $this->logViolation($userId, $text, $word);
                }
                return false;
            }
        }

        return true;
    }

    private function logViolation(int $userId, string $text, string $matchedWord): void
    {
        // Ghi lại log vào Trạm kiểm duyệt AI
        \App\Models\AiModerationLog::create([
            'user_id' => $userId, 
            'ip_address' => request()->ip(),
            'source' => 'rule_based',
            'severity' => 'HIGH',
            'intent' => 'ai_assistant.explicit_violation',
            'blocked_content' => $text,
            'excerpt' => $matchedWord
        ]);
    }
}
