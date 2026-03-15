<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article; // Nhớ import Model Article
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    /**
     * Hiển thị trang chi tiết bài viết
     */
    public function show($slug)
    {
        // 1. Tìm bài viết dựa trên slug
        $article = Article::with('user')
                        ->where('slug', $slug)
                        ->firstOrFail();
        
        // 2. Tăng lượt xem
        $article->increment('view_count');

        // 3. Lấy bài viết liên quan (cùng tag hoặc mới nhất, loại trừ bài hiện tại)
        $relatedArticles = Article::where('id', '!=', $article->id)
            ->where(function($query) use ($article) {
                // Ưu tiên cùng tag nếu có
                if (!empty($article->tag)) {
                    $tags = array_filter(array_map('trim', preg_split('/[,;]+/', $article->tag)));
                    foreach ($tags as $tag) {
                        $query->orWhere('tag', 'like', '%' . $tag . '%');
                    }
                }
            })
            ->orderByDesc('created_at')
            ->take(4)
            ->get();
        
        // Nếu không đủ bài liên quan, lấy thêm bài mới nhất
        if ($relatedArticles->count() < 4) {
            $moreArticles = Article::where('id', '!=', $article->id)
                ->whereNotIn('id', $relatedArticles->pluck('id'))
                ->orderByDesc('created_at')
                ->take(4 - $relatedArticles->count())
                ->get();
            $relatedArticles = $relatedArticles->merge($moreArticles);
        }

        // Gợi ý bài viết - Lấy 4 bài mới nhất khác bài hiện tại
        $suggestedArticles = Article::where('id', '!=', $article->id)
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles', 'suggestedArticles')); 
    }
}