<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'badge_id',
        'earned_at',
        'expires_at',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Kiểm tra badge còn hiệu lực không
     */
    public function isValid()
    {
        if (is_null($this->expires_at)) {
            return true; // Không có hạn = vĩnh viễn
        }
        return $this->expires_at > now();
    }
}
