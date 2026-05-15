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
        'min_recipes',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'min_posts' => 'integer',
        'min_recipes' => 'integer',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Lấy danh hiệu phù hợp nhất cho user dựa trên số bài viết và công thức đã duyệt
     *
     * @param int $publishedPosts Số bài viết đã được duyệt
     * @param int $approvedRecipes Số công thức đã được duyệt
     * @return ActivityTitle|null
     */
    public static function getForUser(int $publishedPosts, int $approvedRecipes): ?self
    {
        return self::where('is_active', true)
            ->where('min_posts', '<=', $publishedPosts)
            ->where('min_recipes', '<=', $approvedRecipes)
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
