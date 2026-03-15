<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\NewBookRequestNotification;

class BookSuggestionController extends Controller
{
    /**
     * Show form to suggest a new book
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('suggest-book', compact('categories'));
    }

    /**
     * Store new book suggestion
     */
    public function store(Request $request)
    {
        // 1. Validate
        $request->validate([
            'title' => 'required|string|max:255',
            'author_name' => 'required|string|max:255',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'cover_image_url' => 'nullable|url|max:500',
        ], [
            'title.required' => 'Vui lòng nhập tên sách.',
            'author_name.required' => 'Vui lòng nhập tên tác giả.',
            'category_ids.*.exists' => 'Danh mục không hợp lệ.',
            'description.max' => 'Mô tả không được quá 2000 ký tự.',
            'cover_image.image' => 'File tải lên phải là hình ảnh.',
            'cover_image.max' => 'Ảnh bìa không được lớn hơn 2MB.',
            'cover_image_url.url' => 'Đường dẫn ảnh không hợp lệ.',
        ]);

        // 2. Xử lý upload ảnh bìa (ưu tiên file, sau đó là URL)
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('books/covers', 'public');
        } elseif ($request->filled('cover_image_url')) {
            $coverPath = $request->cover_image_url;
        }

        // 3. Create slug
        $slug = Str::slug($request->title) . '-' . Str::random(4);

        // 4. Xác định trạng thái: Admin = tự động duyệt, User = chờ duyệt
        $isAdmin = Auth::user()->role === 'admin';
        $isApproved = $isAdmin ? true : false;

        // 5. Create book
        $book = Book::create([
            'title' => $request->title,
            'slug' => $slug,
            'author_name' => $request->author_name,
            'description' => $request->description,
            'cover_image' => $coverPath,
            'is_approved' => $isApproved,
            'created_by_user_id' => Auth::id(),
            'view_count' => 0,
        ]);

        // 6. Attach categories if selected
        if ($request->category_ids && count($request->category_ids) > 0) {
            $book->categories()->attach($request->category_ids);
        }

        // 7. Gửi thông báo cho Admin (chỉ khi user thường đề xuất)
        if (!$isAdmin) {
            try {
                $admins = User::where('role', 'admin')->get();
                Notification::send($admins, new NewBookRequestNotification([
                    'requester_name' => Auth::user()->name,
                    'book_title' => $book->title,
                    'link' => route('admin.books.index') // Hoặc link chi tiết sách chờ duyệt
                ]));
            } catch (\Exception $e) {
                \Log::error("Failed to send notification: " . $e->getMessage());
            }
        }

        // 8. Redirect back to profile with success message
        $message = $isAdmin
            ? 'Đã thêm sách thành công!'
            : 'Đề xuất sách thành công! Vui lòng chờ Admin phê duyệt.';

        return redirect()->route('profile', Auth::id())
            ->with('success', $message);
    }
}
