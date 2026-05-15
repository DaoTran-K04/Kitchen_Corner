<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    protected $fillable = [
        'comment_id',
        'user_id',
        'reason',
        'description',
        'status',
        'admin_note',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Lấy các label tiếng Việt cho lý do báo cáo
     */
    public static function getReasonLabels(): array
    {
        return [
            'spam' => 'Spam',
            'offensive' => 'Ngôn từ xúc phạm',
            'harassment' => 'Quấy rối',
            'inappropriate' => 'Không phù hợp',
            'other' => 'Khác',
        ];
    }

    /**
     * Lấy label tiếng Việt của lý do
     */
    public function getReasonLabelAttribute(): string
    {
        return self::getReasonLabels()[$this->reason] ?? $this->reason;
    }

    /**
     * Bình luận bị báo cáo
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Người báo cáo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin đã xử lý
     */
    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope: Chỉ lấy báo cáo đang chờ xử lý
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
