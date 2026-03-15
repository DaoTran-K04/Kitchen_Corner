<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostRejectedNotification extends Notification
{
    use Queueable;
    protected $post;
    protected $reason;

    public function __construct($post, $reason = null)
    {
        $this->post = $post;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database']; // Lưu vào database
    }

    public function toArray($notifiable)
    {
        $bookTitle = $this->post->book->title ?? 'Sách không xác định';
        $message = 'Bài review "' . $this->post->title . '" về sách "' . $bookTitle . '" đã bị từ chối.';

        if ($this->reason) {
            $message .= ' Lý do: ' . $this->reason;
        }

        return [
            'post_id' => $this->post->id,
            'title' => 'Bài viết bị từ chối',
            'message' => $message,
            'link' => route('profile'), // Link đến profile để xem lại bài viết
            'icon' => 'fas fa-times-circle',
            'color' => 'text-red-500'
        ];
    }
}
