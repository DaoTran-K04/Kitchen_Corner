<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityTitle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'min_posts',
        'min_books',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'min_posts' => 'integer',
        'min_books' => 'integer',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Lấy danh hiệu phù hợp nhất cho user dựa trên số bài viết và sách đã duyệt
     *
     * @param int $publishedPosts Số bài viết đã được duyệt
     * @param int $approvedBooks Số sách đề xuất đã được duyệt
     * @return ActivityTitle|null
     */
    public static function getForUser(int $publishedPosts, int $approvedBooks): ?self
    {
        return self::where('is_active', true)
            ->where('min_posts', '<=', $publishedPosts)
            ->where('min_books', '<=', $approvedBooks)
            ->orderBy('priority', 'desc') // Ưu tiên cao nhất trước
            ->first();
    }

    /**
     * Lấy tất cả danh hiệu đang hoạt động, sắp xếp theo priority
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();
    }
}
