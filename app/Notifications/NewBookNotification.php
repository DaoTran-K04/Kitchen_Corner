<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewBookNotification extends Notification
{
    use Queueable;

    protected $data; // [uploader_name, book_title, link, avatar]

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
            'type' => 'new_book_follower',
            'uploader_name' => $this->data['uploader_name'],
            'book_title' => $this->data['book_title'],
            'link' => $this->data['link'] ?? '#',
            'avatar' => $this->data['avatar'] ?? null,
            'message' => 'vừa đóng góp sách mới: "' . ($this->data['book_title'] ?? '') . '"',
        ];
    }
}
