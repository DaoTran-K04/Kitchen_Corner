<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get user notifications (AJAX)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                // Map data from database to flat structure for JS
                return [
                    'id' => $n->id,
                    'read_at' => $n->read_at,
                    'time' => $n->created_at->diffForHumans(),
                    'link' => $n->data['link'] ?? '#',

                    // Fields for JS rendering
                    'is_system' => isset($n->data['type']) && in_array($n->data['type'], ['new_report', 'recipe_approved', 'recipe_rejected', 'admin_new_post']),

                    'icon' => match ($n->data['type'] ?? '') {
                        'new_report'      => 'fas fa-flag',
                        'recipe_approved' => 'fas fa-check-circle',
                        'recipe_rejected' => 'fas fa-ban',
                        'admin_new_post'  => 'fas fa-file-contract',
                        'comment_like'    => 'fas fa-heart',
                        'comment_reply'   => 'fas fa-reply',
                        'recipe_liked'    => 'fas fa-fire',
                        default           => 'fas fa-bell'
                    },

                    'color' => match ($n->data['type'] ?? '') {
                        'recipe_approved' => 'text-green-600',
                        'recipe_rejected' => 'text-red-600',
                        'admin_new_post'  => 'text-red-600',
                        'comment_like'    => 'text-pink-500',
                        'recipe_liked'    => 'text-orange-500',
                        default           => 'text-yellow-600'
                    },

                    // User info (for post/comment notifications)
                    'user_avatar' => $n->data['avatar'] ?? $n->data['user_avatar'] ?? 'https://api.dicebear.com/7.x/initials/svg?seed=System',
                    'user_name'   => $n->data['user_name'] ?? ($n->data['uploader_name'] ?? ($n->data['author_name'] ?? ($n->data['reporter_name'] ?? 'System'))),

                    // Content
                    'message' => $n->data['message'] ?? '',
                    'title' => match ($n->data['type'] ?? '') {
                        'new_report'      => 'Báo cáo mới',
                        'recipe_approved' => 'Công thức được duyệt',
                        'recipe_rejected' => 'Công thức bị từ chối',
                        'admin_new_post'  => 'Bài đăng mới (Admin)',
                        'comment_like'    => 'Bình luận được thích',
                        'comment_reply'   => 'Có phản hồi mới',
                        'recipe_liked'    => 'Công thức được yêu thích',
                        default           => ''
                    },
                    'post_title' => $n->data['post_title'] ?? ($n->data['recipe_title'] ?? ''),
                ];
            });

        return response()->json([
            'unread_count' => $user->unreadNotifications->count(),
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read and redirect
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();

            // Redirect to the target link
            $link = $notification->data['link'] ?? route('home');
            return redirect($link);
        }

        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
