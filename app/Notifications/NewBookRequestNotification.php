<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookRequestNotification extends Notification
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
            'type' => 'book_request',
            'requester_name' => $this->data['requester_name'] ?? 'System',
            'book_title' => $this->data['book_title'] ?? 'Sách mới',
            'link' => $this->data['link'] ?? '#',
            'message' => ($this->data['requester_name'] ?? 'Ai đó') . ' vừa đề xuất thêm sách: ' . ($this->data['book_title'] ?? ''),
        ];
    }
}
