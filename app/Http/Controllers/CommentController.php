<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
// Thêm dòng này để sử dụng Notification
use App\Notifications\CommentRepliedNotification;

class CommentController extends Controller
{
    public function store(Request $request, $post_id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        try {
            $comment = new Comment();
            $comment->user_id = Auth::id();
            $comment->post_id = $post_id;
            $comment->content = $request->content;
            $comment->parent_id = $request->parent_id;
            $comment->save();

            // --- PHẦN XỬ LÝ THÔNG BÁO (BẮT BUỘC PHẢI CÓ) ---

            if ($comment->parent_id) {
                // Trường hợp: TRẢ LỜI bình luận (Đã hoạt động)
                $parentComment = Comment::find($comment->parent_id);
                if ($parentComment && $parentComment->user_id != Auth::id()) {
                    $parentComment->user->notify(new \App\Notifications\CommentRepliedNotification(Auth::user(), $comment));
                }
            } else {
                // Trường hợp: BÌNH LUẬN MỚI cho bài viết (CHỖ CẦN SỬA)
                // Tìm bài post dựa trên $post_id được truyền từ Route
                $post = Post::find($post_id);

                // Nếu tìm thấy Post và chủ bài Post không phải là người đang comment
                if ($post && $post->user_id != Auth::id()) {
                    $post->user->notify(new \App\Notifications\PostCommentedNotification(Auth::user(), $post));
                }
            }

            // ----------------------------------------------

            $equippedFrame = Auth::user()->equippedFrame();
            $frameUrl = null;
            if ($equippedFrame) {
                $frameUrl = \Illuminate\Support\Str::startsWith($equippedFrame->frame_image, 'http')
                    ? $equippedFrame->frame_image
                    : asset('storage/' . $equippedFrame->frame_image);
            }

            $newCount = Comment::where('post_id', $post_id)->count();

            return response()->json([
                'success' => true,
                'message' => 'Gửi bình luận thành công!',
                'new_count' => $newCount,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'parent_id' => $comment->parent_id,
                    'user_name' => Auth::user()->name,
                    'user_avatar' => Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random',
                    'user_frame' => $frameUrl,
                    'created_at' => 'Vừa xong'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}