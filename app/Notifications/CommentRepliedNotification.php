<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CommentRepliedNotification extends Notification //implements ShouldQueue
{
    use Queueable;

    protected $sender; // Người trả lời
    protected $reply;  // Nội dung trả lời mới

    public function __construct($sender, $reply)
    {
        $this->sender = $sender;
        $this->reply = $reply;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $recipe = $this->reply->recipe;
        $slug = $recipe ? $recipe->slug : '404';

        return [
            'user_id' => $this->sender->id,
            'user_name' => $this->sender->name,
            'user_avatar' => $this->sender->avatar,
            'message' => 'đã trả lời bình luận của bạn',
            'post_title' => $this->reply->content,
            'link' => route('recipes.show', ['slug' => $slug]) . '#comment-' . $this->reply->id
        ];
    }
}