<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'role',
        'is_active',
        'violation_count',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // --- 1. CÁC MỐI QUAN HỆ CƠ BẢN ---

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }



    // --- 2. QUAN HỆ FOLLOW ---

    public function followings()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function isFollowing($userId)
    {
        return $this->followings()->where('following_id', $userId)->exists();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // --- 3. LOGIC THỬ THÁCH VÀ DANH HIỆU (BADGES & CHALLENGES) ---

    // Quan hệ với Badge (Huy hiệu)
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at', 'expires_at')
            ->withTimestamps();
    }

    // Lấy danh hiệu còn hiệu lực, sắp xếp theo thứ tự display_order
    public function activeBadges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at', 'expires_at', 'display_order')
            ->where(function ($query) {
                $query->where('expires_at', '>', now())
                    ->orWhereNull('expires_at');
            })
            ->orderByPivot('display_order', 'asc')
            ->withTimestamps();
    }


    // Quan hệ với Thử thách
    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'user_challenges')
            ->withPivot('current_count', 'is_completed', 'completed_at')
            ->withTimestamps();
    }

    // Quan hệ với Avatar Frame (Khung avatar)
    public function avatarFrames()
    {
        return $this->belongsToMany(AvatarFrame::class, 'user_avatar_frames')
            ->withPivot('is_equipped', 'earned_at')
            ->withTimestamps();
    }

    // Lấy khung avatar đang sử dụng
    public function equippedFrame()
    {
        return $this->avatarFrames()
            ->wherePivot('is_equipped', true)
            ->first();
    }

    // --- 5. LẤY DANH HIỆU HOẠT ĐỘNG (ACTIVITY TITLE) ---
    /**
     * Lấy danh hiệu dựa trên số công thức đã duyệt
     *
     * @return \App\Models\ActivityTitle|null
     */
    public function getActivityTitle()
    {
        $publishedRecipes = $this->recipes()->where('status', 'published')->count();
        // Since we removed books, we pass 0 or remove that parameter in ActivityTitle.
        // Assuming ActivityTitle::getForUser will be updated to handle (publishedRecipes).
        return ActivityTitle::getForUser($publishedRecipes, 0);
    }

    // --- 4. HÀM TÍNH ĐIỂM THỬ THÁCH (CHUẨN XÁC) ---
    public function updateChallengeProgress()
    {
        // Lấy tất cả thử thách user đã tham gia
        $joinedChallenges = $this->challenges;

        foreach ($joinedChallenges as $challenge) {

            // Xử lý ngày tháng an toàn:
            // Bắt đầu từ 00:00:00 của ngày start
            $startDate = Carbon::parse($challenge->start_date)->startOfDay();
            // Kết thúc lúc 23:59:59 của ngày end
            $endDate = Carbon::parse($challenge->end_date)->endOfDay();

            // Đếm số lượng hành động hợp lệ dựa trên action_type
            $validActionCount = 0;

            if ($challenge->action_type === 'post_comment') {
                // Tính số comment của user
                $validActionCount = $this->comments()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
            } else {
                // Tính số bài viết đã duyệt
                $validActionCount = $this->recipes()
                    ->where('status', 'published')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
            }

            // Kiểm tra điều kiện hoàn thành
            $isCompleted = $validActionCount >= $challenge->target_count;

            // Chuẩn bị dữ liệu cập nhật
            $pivotData = [
                'current_count' => $validActionCount,
                'is_completed' => $isCompleted
            ];

            // Nếu vừa hoàn thành xong thì ghi nhận ngày hoàn thành
            if ($isCompleted && !$challenge->pivot->completed_at) {
                $pivotData['completed_at'] = now();
            }

            // Lưu vào DB
            $this->challenges()->updateExistingPivot($challenge->id, $pivotData);

            // Trao phần thưởng nếu hoàn thành
            if ($isCompleted) {
                // Trao huy hiệu (Badge)
                if (!$this->badges()->where('badge_id', $challenge->badge_id)->exists()) {
                    $this->badges()->attach($challenge->badge_id, [
                        'earned_at' => now()
                    ]);
                }

                // Trao khung avatar (Frame) nếu có
                if ($challenge->avatar_frame_id && !$this->avatarFrames()->where('avatar_frame_id', $challenge->avatar_frame_id)->exists()) {
                    $this->avatarFrames()->attach($challenge->avatar_frame_id, [
                        'earned_at' => now(),
                        'is_equipped' => false
                    ]);
                }
            }
        }
    }
}
