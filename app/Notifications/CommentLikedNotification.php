<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Nên dùng Queue để web chạy mượt
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class CommentLikedNotification extends Notification //implements ShouldQueue
{
    use Queueable;

    protected $user; // Người vừa bấm like
    protected $comment; // Comment được like

    public function __construct($user, $comment)
    {
        $this->user = $user;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database']; // Lưu vào database
    }

    public function toDatabase($notifiable)
    {
        $recipe = $this->comment->recipe;
        $slug = $recipe ? $recipe->slug : '404';

        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_avatar' => $this->user->avatar,
            'message' => 'đã thích bình luận của bạn',
            'post_title' => $this->comment->content,
            'link' => route('recipes.show', ['slug' => $slug]) . '#comment-' . $this->comment->id
        ];
    }
}