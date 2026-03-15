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
                    'is_system' => isset($n->data['type']) && in_array($n->data['type'], ['new_report', 'book_request', 'book_approved', 'admin_new_post']),

                    'icon' => match ($n->data['type'] ?? '') {
                        'new_report' => 'fas fa-flag',
                        'book_request' => 'fas fa-book',
                        'book_approved' => 'fas fa-check-circle',
                        'admin_new_post' => 'fas fa-file-contract',
                        'new_book_follower' => 'fas fa-book-open',
                        default => 'fas fa-bell'
                    },

                    'color' => match ($n->data['type'] ?? '') {
                        'book_approved' => 'text-green-600',
                        'admin_new_post' => 'text-red-600',
                        default => 'text-yellow-600'
                    },

                    // User info (for post/comment notifications)
                    'user_avatar' => $n->data['avatar'] ?? 'https://ui-avatars.com/api/?name=System',
                    'user_name' => $n->data['uploader_name'] ?? ($n->data['author_name'] ?? ($n->data['reporter_name'] ?? ($n->data['requester_name'] ?? 'System'))),

                    // Content
                    'message' => $n->data['message'] ?? '',
                    'title' => match ($n->data['type'] ?? '') {
                        'new_report' => 'Báo cáo mới',
                        'book_request' => 'Gợi ý sách mới',
                        'book_approved' => 'Sách được duyệt',
                        'admin_new_post' => 'Bài đăng mới (Admin)',
                        'new_book_follower' => 'Sách mới từ người dùng',
                        default => ''
                    },
                    'post_title' => $n->data['post_title'] ?? ($n->data['book_title'] ?? ''),
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