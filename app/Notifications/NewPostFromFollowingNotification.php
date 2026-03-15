<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Post;
use App\Models\User;

class NewPostFromFollowingNotification extends Notification
{
    use Queueable;

    protected $post;
    protected $author;

    public function __construct(Post $post, User $author)
    {
        $this->post = $post;
        $this->author = $author;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $bookTitle = $this->post->book->title ?? 'Sách không xác định';

        return [
            'post_id' => $this->post->id,
            'user_id' => $this->author->id,
            'user_name' => $this->author->name,
            'user_avatar' => $this->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->author->name),
            'title' => 'Bài viết mới từ người bạn theo dõi',
            'message' => 'đã đăng bài review mới về sách "' . $bookTitle . '"',
            'post_title' => $this->post->title,
            'link' => route('book.reviews', $this->post->book->slug ?? '#') . '#post-' . $this->post->id,
            'icon' => 'fas fa-rss',
            'color' => 'text-blue-500'
        ];
    }
}
