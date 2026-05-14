<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Recipe;

class RecipeRejectedNotification extends Notification
{
    use Queueable;

    protected $recipe;
    protected $reason;
    protected $isViolation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Recipe $recipe, $reason, $isViolation = false)
    {
        $this->recipe = $recipe;
        $this->reason = $reason;
        $this->isViolation = $isViolation;
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
            'type' => 'recipe_rejected',
            'recipe_id' => $this->recipe->id,
            'book_title' => $this->recipe->title, // Để tương thích với code cũ ở getNotifications
            'reason' => $this->reason,
            'is_violation' => $this->isViolation,
            'message' => 'Công thức của bạn đã bị từ chối do: ' . $this->reason,
            'icon' => 'fas fa-ban',
            'color' => 'text-red-600',
            'title' => $this->isViolation ? 'Cảnh báo vi phạm!' : 'Công thức bị từ chối',
        ];
    }
}
