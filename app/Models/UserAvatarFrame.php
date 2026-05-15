<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAvatarFrame extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_frame_id',
        'is_equipped',
        'earned_at',
    ];

    protected $casts = [
        'is_equipped' => 'boolean',
        'earned_at' => 'datetime',
    ];

    /**
     * User sở hữu frame
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Avatar frame
     */
    public function avatarFrame()
    {
        return $this->belongsTo(AvatarFrame::class);
    }
}
