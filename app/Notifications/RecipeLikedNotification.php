<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RecipeLikedNotification extends Notification
{
    use Queueable;

    protected $user; // Người vừa bấm like
    protected $recipe; // Công thức được like

    public function __construct($user, $recipe)
    {
        $this->user = $user;
        $this->recipe = $recipe;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_avatar' => $this->user->avatar,
            'message' => 'đã thích công thức của bạn',
            'post_title' => $this->recipe->title,
            'link' => route('recipes.show', $this->recipe->slug),
        ];
    }
}
