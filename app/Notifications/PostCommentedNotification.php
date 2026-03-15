<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Post;
use App\Models\User;

class PostCommentedNotification extends Notification
{
    use Queueable;

    protected $user; // Người bình luận
    protected $post; // Bài viết được bình luận

    public function __construct(User $user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'user_name' => $this->user->name,
            'user_avatar' => $this->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($this->user->name),
            'message' => 'đã bình luận về bài review của bạn.',
            'post_title' => $this->post->title,
            'link' => route('book.reviews', $this->post->book->slug) . '#post-' . $this->post->id,
            'type' => 'post_comment'
        ];
    }
}