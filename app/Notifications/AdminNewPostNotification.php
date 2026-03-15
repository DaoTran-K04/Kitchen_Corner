<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNewPostNotification extends Notification
{
    use Queueable;

    protected $data; // [author_name, post_title, link, avatar]

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
            'type' => 'admin_new_post',
            'author_name' => $this->data['author_name'],
            'post_title' => $this->data['post_title'],
            'link' => $this->data['link'] ?? '#',
            'avatar' => $this->data['avatar'] ?? null,
            'message' => ($this->data['author_name'] ?? 'User') . ' vừa đăng bài: "' . ($this->data['post_title'] ?? '') . '"',
        ];
    }
}
