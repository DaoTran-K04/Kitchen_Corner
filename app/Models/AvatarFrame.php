<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvatarFrame extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'frame_image',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Các thử thách sử dụng frame này làm phần thưởng
     */
    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    /**
     * Users đã nhận frame này
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_avatar_frames')
            ->withPivot('is_equipped', 'earned_at')
            ->withTimestamps();
    }

    /**
     * User avatar frame records
     */
    public function userAvatarFrames()
    {
        return $this->hasMany(UserAvatarFrame::class);
    }
}
