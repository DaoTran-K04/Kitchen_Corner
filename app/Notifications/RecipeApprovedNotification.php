<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecipeApprovedNotification extends Notification
{
    use Queueable;

    protected $recipe;

    public function __construct($recipe)
    {
        $this->recipe = $recipe;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'book_approved',
            'title' => 'Công thức được duyệt',
            'message' => "Công thức \"{$this->recipe->title}\" của bạn đã được phê duyệt và hiển thị công khai.",
            'post_title' => $this->recipe->title,
            'link' => route('recipes.show', $this->recipe->slug),
            'icon' => 'fas fa-check-circle',
            'color' => 'text-green-600',
        ];
    }
}
