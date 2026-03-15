<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function topLikedPosts()
    {
        $posts = Post::query()
            ->published()
            ->mostLiked()
            ->take(10)
            ->with([
                'user:id,name,avatar',
                'book:id,title,slug,cover_image'
            ])
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách Top 10 bài viết được yêu thích nhất thành công!',
            'data' => $posts
        ]);
    }
}
