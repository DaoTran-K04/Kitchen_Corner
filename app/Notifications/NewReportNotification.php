<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReportNotification extends Notification
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
            'type' => 'new_report',
            'reporter_name' => $this->data['reporter_name'] ?? 'System',
            'target_type' => $this->data['target_type'] ?? 'content', // post, comment
            'link' => $this->data['link'] ?? '#',
            'message' => 'Có báo cáo vi phạm mới từ ' . ($this->data['reporter_name'] ?? 'thành viên'),
        ];
    }
}
