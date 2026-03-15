<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookApprovedNotification extends Notification
{
    use Queueable;

    protected $data; // [book_title, link]

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'book_approved',
            'book_title' => $this->data['book_title'],
            'link' => $this->data['link'] ?? '#',
            'message' => 'Sách bạn đề xuất "' . $this->data['book_title'] . '" đã được duyệt!',
        ];
    }
}
