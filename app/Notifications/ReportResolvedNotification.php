<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportResolvedNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'report_resolved',
            'status' => $this->data['status'] ?? 'resolved', // approved or rejected
            'message' => $this->data['message'] ?? 'Báo cáo của bạn đã được xử lý.',
            'admin_note' => $this->data['admin_note'] ?? null,
            'post_title' => $this->data['post_title'] ?? null,
            'link' => $this->data['link'] ?? '/',
        ];
    }
}
