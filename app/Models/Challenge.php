<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'badge_id',
        'avatar_frame_id',
        'name',
        'slug',
        'description',
        'target_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'target_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Badge nhận được khi hoàn thành
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    /**
     * Avatar Frame nhận được khi hoàn thành (optional)
     */
    public function avatarFrame()
    {
        return $this->belongsTo(AvatarFrame::class);
    }

    /**
     * Users tham gia challenge
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_challenges')
            ->withPivot('current_count', 'is_completed', 'completed_at')
            ->withTimestamps();
    }

    /**
     * User challenges records
     */
    public function userChallenges()
    {
        return $this->hasMany(UserChallenge::class);
    }

    /**
     * Kiểm tra challenge đang trong thời gian hoạt động
     */
    public function isOngoing()
    {
        $today = now()->toDateString();
        return $this->is_active
            && $this->start_date <= $today
            && $this->end_date >= $today;
    }
}
