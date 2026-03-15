<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostNotification extends Notification
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_post',
            'author_name' => $this->data['author_name'] ?? 'System',
            'post_title' => $this->data['post_title'] ?? 'Bài review mới',
            'link' => $this->data['link'] ?? '#',
            'message' => ($this->data['author_name'] ?? 'Người bạn theo dõi') . ' vừa đăng bài review mới: ' . ($this->data['post_title'] ?? ''),
            'avatar' => $this->data['avatar'] ?? null,
        ];
    }
}
