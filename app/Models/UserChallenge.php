<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'current_count',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'current_count' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    /**
     * Tính phần trăm hoàn thành
     */
    public function getProgressPercentAttribute()
    {
        $target = $this->challenge->target_count;
        if ($target <= 0)
            return 100;

        $percent = ($this->current_count / $target) * 100;
        return min($percent, 100);
    }
}
