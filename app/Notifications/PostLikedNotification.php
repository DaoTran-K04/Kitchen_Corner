<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Post;
use App\Models\User;

class PostLikedNotification extends Notification
{
    use Queueable;

    protected $user; // Người nhấn thích
    protected $post; // Bài viết được thích

    public function __construct(User $user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    public function via($notifiable)
    {
        // Lưu vào bảng notifications trong database
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'user_name' => $this->user->name,
            'user_avatar' => $this->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($this->user->name),
            'message' => 'đã thích bài review của bạn.',
            'post_title' => $this->post->title,
            // Đường dẫn đến trang chi tiết bài viết (slug của sách hoặc id bài viết)
            'link' => route('book.reviews', $this->post->book->slug) . '#post-' . $this->post->id,
            'type' => 'post_like'
        ];
    }
}