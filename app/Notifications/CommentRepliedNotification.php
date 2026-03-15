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
        // 1. Lấy thông tin sách để tạo Link
        // Reply -> Post -> Book
        $post = $this->reply->post;
        $book = $post ? $post->book : null;

        $slug = '404'; 
        if ($book) {
            $slug = $book->slug;
        }

        return [
            'user_id' => $this->sender->id,
            'user_name' => $this->sender->name,
            'user_avatar' => $this->sender->avatar,
            
            // Thông điệp khác với Like
            'message' => 'đã trả lời bình luận của bạn',
            
            // Hiển thị nội dung câu trả lời
            'post_title' => $this->reply->content,
            
            // Link trỏ thẳng tới câu trả lời mới
            'link' => route('detail', ['slug' => $slug]) . '#comment-' . $this->reply->id
        ];
    }
}