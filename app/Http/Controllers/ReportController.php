<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\PostReport;
use App\Models\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\NewReportNotification;

class ReportController extends Controller
{
    /**
     * Report một bài viết
     */
    public function reportPost(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để báo cáo!'], 401);
        }

        $post = Post::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Bài viết không tồn tại!'], 404);
        }

        // Không cho phép report bài của chính mình
        if ($post->user_id === $user->id) {
            return response()->json(['success' => false, 'message' => 'Bạn không thể báo cáo bài viết của chính mình!'], 403);
        }

        // Kiểm tra đã report chưa
        $existingReport = PostReport::where('post_id', $id)->where('user_id', $user->id)->first();
        if ($existingReport) {
            return response()->json(['success' => false, 'message' => 'Bạn đã báo cáo bài viết này trước đó!'], 400);
        }

        $request->validate([
            'reason' => 'required|in:spam,offensive,harassment,inappropriate,copyright,other',
            'description' => 'nullable|string|max:500',
        ]);

        PostReport::create([
            'post_id' => $id,
            'user_id' => $user->id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // Gửi thông báo cho Admin
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewReportNotification([
                'reporter_name' => $user->name,
                'target_type' => 'post',
                'link' => route('post-reports.index') // Link admin
            ]));
        } catch (\Exception $e) {
            \Log::error("Failed to send notification: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã báo cáo! Chúng tôi sẽ xem xét và xử lý sớm nhất.',
        ]);
    }

    /**
     * Report một bình luận
     */
    public function reportComment(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập để báo cáo!'], 401);
        }

        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Bình luận không tồn tại!'], 404);
        }

        // Không cho phép report comment của chính mình
        if ($comment->user_id === $user->id) {
            return response()->json(['success' => false, 'message' => 'Bạn không thể báo cáo bình luận của chính mình!'], 403);
        }

        // Kiểm tra đã report chưa
        $existingReport = CommentReport::where('comment_id', $id)->where('user_id', $user->id)->first();
        if ($existingReport) {
            return response()->json(['success' => false, 'message' => 'Bạn đã báo cáo bình luận này trước đó!'], 400);
        }

        $request->validate([
            'reason' => 'required|in:spam,offensive,harassment,inappropriate,other',
            'description' => 'nullable|string|max:500',
        ]);

        CommentReport::create([
            'comment_id' => $id,
            'user_id' => $user->id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // Gửi thông báo cho Admin
        try {
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new NewReportNotification([
                'reporter_name' => $user->name,
                'target_type' => 'comment',
                'link' => '/admin/comment-reports' // Hardcode link vi namespace route admin
            ]));
        } catch (\Exception $e) {
            \Log::error("Failed to send notification: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã báo cáo! Chúng tôi sẽ xem xét và xử lý sớm nhất.',
        ]);
    }
}
