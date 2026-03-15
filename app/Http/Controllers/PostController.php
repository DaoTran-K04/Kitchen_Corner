<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Notifications\NewLikeNotification;
use App\Notifications\PostCommentedNotification;
use App\Notifications\CommentLikedNotification;
use App\Notifications\NewPostNotification;
use App\Notifications\AdminNewPostNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class PostController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|numeric|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|min:10',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'thumbnail_url' => 'nullable|url|max:500',
        ], [
            'book_id.required' => 'Vui lòng chọn một cuốn sách để review.',
            'book_id.exists' => 'Vui lòng chọn một cuốn sách hợp lệ.',
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.numeric' => 'Điểm đánh giá phải là số từ 1 đến 5.',
            'rating.min' => 'Điểm đánh giá phải từ 1 sao trở lên.',
            'rating.max' => 'Điểm đánh giá không được quá 5 sao.',
            'title.required' => 'Bạn chưa nhập tiêu đề bài viết.',
            'content.required' => 'Bạn chưa nhập nội dung bài review.',
            'content.min' => 'Nội dung review quá ngắn (tối thiểu 10 ký tự).',
            'thumbnail.image' => 'File tải lên phải là hình ảnh.',
            'thumbnail.max' => 'Ảnh không được lớn hơn 2MB.',
            'thumbnail_url.url' => 'Đường dẫn ảnh không hợp lệ.',
        ]);

        // 2. Xử lý upload thumbnail (ưu tiên file, sau đó là URL)
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('posts/thumbnails', 'public');
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnailPath = $request->thumbnail_url;
        }

        // 3. Tạo Slug
        $slug = Str::slug($request->title) . '-' . time();

        // 4. Xác định trạng thái: Admin = tự động duyệt, User = chờ duyệt
        $isAdmin = Auth::user()->role === 'admin';
        $status = $isAdmin ? 'published' : 'pending';

        // 5. Lưu vào Database
        $post = Post::create([

            'user_id' => Auth::id(),
            'book_id' => $request->input('book_id'),
            'title' => $request->input('title'),
            'slug' => $slug,
            'rating' => $request->input('rating'),
            'content' => $request->input('content'),
            'thumbnail' => $thumbnailPath,
            'status' => $status,
        ]);

        // 6. Cập nhật tiến độ Thử Thách (Chỉ khi bài đã được duyệt - admin)
        if ($post->status == 'published') {
            Auth::user()->updateChallengeProgress();
        }

        // 7. Thông báo phù hợp với trạng thái
        $message = $isAdmin
            ? 'Bài viết đã được đăng thành công!'
            : 'Đã gửi bài viết! Vui lòng chờ Admin phê duyệt.';

        // 8. Gửi thông báo cho Followers (Nếu bài viết được published ngay)
        if ($post->status == 'published') {
            try {
                $followers = Auth::user()->followers;
                // Có thể lọc những người đã tắt thông báo nếu có tính năng đó
                Notification::send($followers, new NewPostNotification([
                    'author_name' => Auth::user()->name,
                    'post_title' => $post->title,
                    'link' => route('detail', $post->book->slug), // Link đến sách
                    'avatar' => Auth::user()->avatar
                ]));
            } catch (\Exception $e) {
                \Log::error("Failed to send follower notification: " . $e->getMessage());
            }
        }

        // 9. Gửi cảnh báo cho Admin (New Post Alert)
        try {
            $admins = User::where('role', 'admin')->get();
            // Thêm trạng thái vào tiêu đề
            $statusMsg = $post->status == 'pending' ? ' (Chờ duyệt)' : '';

            Notification::send($admins, new AdminNewPostNotification([
                'author_name' => Auth::user()->name,
                'post_title' => $post->title . $statusMsg,
                'link' => route('detail', $post->book->slug),
                'avatar' => Auth::user()->avatar
            ]));
        } catch (\Exception $e) {
            \Log::error("Failed to send admin notification: " . $e->getMessage());
        }

        return redirect()->route('profile', Auth::id())
            ->with('success', $message);
    }

    // Toggle Like (Giữ nguyên)
    public function toggleLike($id)
    {
        $user = Auth::user();
        if (!$user)
            return response()->json(['error' => 'Bạn cần đăng nhập!'], 401);

        $post = Post::find($id);
        if (!$post)
            return response()->json(['error' => 'Bài viết không tồn tại!'], 404);

        // Tìm like
        $like = Like::where('user_id', $user->id)->where('post_id', $id)->first();
        $liked = false;

        if ($like) {
            $like->delete(); // Unlike
            $liked = false;
        } else {
            Like::create(['user_id' => $user->id, 'post_id' => $id]); // Like
            $liked = true;

            // Gửi thông báo (Trừ khi tự like bài mình)
            if ($post->user_id != $user->id) {
                $post->user->notify(new CommentLikedNotification($user, $post));
            }
        }

        // Đếm lại số like
        $count = Like::where('post_id', $id)->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'count' => $count
        ]);
    }

    // Post Comment (Giữ nguyên)
    public function postComment(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user)
            return response()->json(['error' => 'Bạn cần đăng nhập!'], 401);

        $request->validate(['content' => 'required']);

        $post = Post::find($id);
        if (!$post)
            return response()->json(['error' => 'Bài viết không tồn tại!'], 404);

        // Lưu comment
        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $id,
            'content' => $request->input('content')
        ]);

        // Gửi thông báo (Trừ khi tự comment bài mình)
        if ($post->user_id != $user->id) {
            $post->user->notify(new PostCommentedNotification($user, $post));
        }

        $equippedFrame = $user->equippedFrame();
        $frameUrl = null;
        if ($equippedFrame) {
            $frameUrl = \Illuminate\Support\Str::startsWith($equippedFrame->frame_image, 'http')
                ? $equippedFrame->frame_image
                : asset('storage/' . $equippedFrame->frame_image);
        }

        // Đếm lại số comment
        $commentCount = Comment::where('post_id', $id)->count();

        return response()->json([
            'success' => true,
            'comment_id' => $comment->id,
            'user_name' => $user->name,
            'user_avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
            'user_frame' => $frameUrl,
            'content' => $comment->content,
            'count' => $commentCount,
            'created_at' => 'Vừa xong'
        ]);
    }

    // Hiển thị form chỉnh sửa bài review
    public function edit($id)
    {
        $user = Auth::user();
        $post = Post::with('book')->findOrFail($id);

        // Không cho phép sửa nếu bài viết đang chờ duyệt xóa
        if ($post->status === 'pending_delete') {
            return redirect()->back()->with('error', 'Bài viết đang chờ duyệt xóa, không thể chỉnh sửa.');
        }

        // Chỉ cho phép chủ bài viết hoặc admin sửa
        $isAdmin = $user->role === 'admin';
        if (!$isAdmin && (int) $post->user_id !== (int) $user->id) {
            return redirect()->back()->with('error', 'Bạn không có quyền sửa bài viết này.');
        }

        // Không cho sửa bài viết đang chờ xóa
        if ($post->status === 'pending_delete') {
            return redirect()->back()->with('error', 'Không thể sửa bài viết đang chờ xóa!');
        }

        return view('edit-review', compact('user', 'post'));
    }

    // Cập nhật bài review
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $post = Post::findOrFail($id);

        // Không cho phép sửa nếu bài viết đang chờ duyệt xóa
        if ($post->status === 'pending_delete') {
            return redirect()->back()->with('error', 'Bài viết đang chờ duyệt xóa, không thể chỉnh sửa.');
        }

        // Chỉ cho phép chủ bài viết hoặc admin sửa
        $isAdmin = $user->role === 'admin';
        if (!$isAdmin && (int) $post->user_id !== (int) $user->id) {
            return redirect()->back()->with('error', 'Bạn không có quyền sửa bài viết này.');
        }

        // Validate dữ liệu
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|min:10',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
            'thumbnail_url' => 'nullable|url|max:500',
        ], [
            'title.required' => 'Bạn chưa nhập tiêu đề bài viết.',
            'content.min' => 'Nội dung review quá ngắn.',
            'thumbnail.image' => 'File tải lên phải là hình ảnh.',
            'thumbnail.max' => 'Ảnh không được lớn hơn 2MB.',
            'thumbnail_url.url' => 'Đường dẫn ảnh không hợp lệ.',
        ]);

        // Xử lý upload thumbnail (ưu tiên file, sau đó là URL)
        $thumbnailPath = $post->thumbnail; // Giữ ảnh cũ nếu không thay đổi
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('posts/thumbnails', 'public');
        } elseif ($request->filled('thumbnail_url')) {
            $thumbnailPath = $request->thumbnail_url;
        }

        // Xác định trạng thái: Admin sửa = tự động duyệt, User sửa = chờ duyệt lại
        // Biến $isAdmin đã được định nghĩa ở trên
        $status = $isAdmin ? 'published' : 'pending';

        // Cập nhật bài viết
        $post->update([
            'title' => $request->input('title'),
            'rating' => $request->input('rating'),
            'content' => $request->input('content'),
            'thumbnail' => $thumbnailPath,
            'status' => $status,
        ]);

        // Thông báo phù hợp với trạng thái
        $message = $isAdmin
            ? 'Bài viết đã được cập nhật thành công!'
            : 'Bài viết đã được cập nhật! Vui lòng chờ Admin phê duyệt lại.';

        return redirect()->route('profile', $user->id)
            ->with('success', $message);
    }

    // Yêu cầu xóa bài review (chờ admin duyệt) hoặc xóa ngay nếu admin tự xóa bài của mình
    public function requestDelete($id)
    {
        $user = Auth::user();
        $post = Post::findOrFail($id);

        // Chỉ cho phép chủ bài viết yêu cầu xóa
        if ((int) $post->user_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bài viết này.'
            ], 403);
        }

        // Nếu admin xóa bài của chính mình -> xóa ngay (soft delete)
        if ($user->role === 'admin') {
            $post->delete(); // Soft delete
            return response()->json([
                'success' => true,
                'message' => 'Bài viết đã được xóa thành công!'
            ]);
        }

        // User thường: cập nhật status thành pending_delete, chờ admin duyệt
        $post->update(['status' => 'pending_delete']);

        // Gửi thông báo cho admin
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new AdminNewPostNotification([
                'author_name' => $user->name,
                'post_title' => '[Yêu cầu xóa] ' . $post->title,
                'link' => route('admin.posts.index', ['status' => 'pending_delete']),
                'avatar' => $user->avatar
            ]));
        } catch (\Exception $e) {
            \Log::error("Failed to send delete request notification: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu xóa đã được gửi! Vui lòng chờ Admin xử lý.'
        ]);
    }

    // Hủy yêu cầu xóa bài review
    public function cancelDelete($id)
    {
        $user = Auth::user();
        $post = Post::findOrFail($id);

        // Chỉ cho phép chủ bài viết hủy yêu cầu xóa
        if ((int) $post->user_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        // Chỉ cho phép hủy nếu đang ở trạng thái pending_delete
        if ($post->status !== 'pending_delete') {
            return response()->json([
                'success' => false,
                'message' => 'Bài viết không ở trạng thái chờ xóa.'
            ], 400);
        }

        // Chuyển status về published
        $post->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => 'Đã hủy yêu cầu xóa! Bài viết đã được khôi phục.'
        ]);
    }

    // Khôi phục bài review từ thùng rác
    public function restorePost($id)
    {
        $user = Auth::user();
        $post = Post::onlyTrashed()->findOrFail($id);

        // Chỉ cho phép chủ bài viết khôi phục
        if ((int) $post->user_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục bài viết này.'
            ], 403);
        }

        // Khôi phục bài viết
        $post->restore();
        $post->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => 'Đã khôi phục bài viết thành công!'
        ]);
    }

    // Xóa vĩnh viễn bài review khỏi thùng rác
    public function forceDeletePost($id)
    {
        $user = Auth::user();
        $post = Post::onlyTrashed()->findOrFail($id);

        // Chỉ cho phép chủ bài viết xóa vĩnh viễn
        if ((int) $post->user_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xóa bài viết này.'
            ], 403);
        }

        // Xóa vĩnh viễn
        $post->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa vĩnh viễn bài viết!'
        ]);
    }
}