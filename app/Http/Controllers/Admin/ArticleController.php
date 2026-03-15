<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    // Hiển thị danh sách (nếu cần sau này)
    public function index()
    {
        $articles = Article::latest()->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }
    public function show($slug)
    {
        $query = Article::where('slug', $slug);

        // Nếu không phải admin thì chỉ hiển thị bài viết đang active
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            $query->where('is_active', true);
        }

        $article = $query->firstOrFail();

        // Tăng lượt xem
        $article->increment('view_count');

        // Lấy bài viết liên quan (cùng tag hoặc mới nhất, loại trừ bài hiện tại)
        $relatedArticles = Article::where('id', '!=', $article->id)
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        // Gợi ý bài viết - Lấy 4 bài mới nhất khác bài hiện tại
        $suggestedArticles = Article::where('id', '!=', $article->id)
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles', 'suggestedArticles'));
    }

    // Hiển thị form tạo bài viết mới
    public function create()
    {
        return view('admin.articles.create');
    }

    // Xử lý lưu bài viết mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'tag' => 'nullable|max:50',
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'thumbnail_url' => 'nullable|url|max:500',
        ]);

        $data = $request->except(['thumbnail', 'thumbnail_url']);
        $data['slug'] = Str::slug($request->title) . '-' . time();
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');
        $data['user_id'] = auth()->id();
        $data['view_count'] = 0;

        // Ensure only ONE article can be featured at a time
        if ($data['is_featured']) {
            Article::where('is_featured', true)->update(['is_featured' => false]);
        }

        // Xử lý upload ảnh hoặc URL
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('articles', 'public');
            $data['thumbnail'] = $path;
        } elseif ($request->filled('thumbnail_url')) {
            $data['thumbnail'] = $request->thumbnail_url;
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('success', 'Tạo bài viết thành công!');
    }

    // Hiển thị form chỉnh sửa
    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('admin.articles.edit', compact('article'));
    }

    // Xử lý cập nhật dữ liệu
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'tag' => 'nullable|max:50',
            'excerpt' => 'nullable|max:500',
            'content' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'thumbnail_url' => 'nullable|url|max:500',
        ]);

        $data = $request->except(['thumbnail', 'thumbnail_url']);
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');

        // Ensure only ONE article can be featured at a time
        if ($data['is_featured']) {
            Article::where('is_featured', true)
                ->where('id', '!=', $article->id)
                ->update(['is_featured' => false]);
        }

        // Xử lý upload ảnh mới hoặc URL
        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh cũ nếu không phải link online
            if ($article->thumbnail && !Str::startsWith($article->thumbnail, 'http')) {
                Storage::delete('public/' . $article->thumbnail);
            }
            $path = $request->file('thumbnail')->store('articles', 'public');
            $data['thumbnail'] = $path;
        } elseif ($request->filled('thumbnail_url')) {
            // Xóa ảnh cũ nếu không phải link online
            if ($article->thumbnail && !Str::startsWith($article->thumbnail, 'http')) {
                Storage::delete('public/' . $article->thumbnail);
            }
            $data['thumbnail'] = $request->thumbnail_url;
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    // Xóa bài viết
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $articleData = $article->toArray();

        // Với soft delete, giữ ảnh để có thể restore

        $article->delete();

        // Ghi log để có thể khôi phục
        \App\Models\AdminActivityLog::log(
            'delete',
            "Xóa Bài viết: {$articleData['title']}",
            Article::class,
            $articleData['id'],
            $articleData,
            null
        );

        return redirect()->back()->with('success', 'Đã xóa bài viết!');
    }
}