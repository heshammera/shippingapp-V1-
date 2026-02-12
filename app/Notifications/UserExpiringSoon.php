<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserExpiringSoon extends Notification
{
    use Queueable;

    public $daysLeft;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $daysLeft)
    {
        $this->daysLeft = $daysLeft;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Can add 'mail' here too if we want to use Notification for email instead of separate Mailable
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تنبيه انتهاء الصلاحية',
            'message' => "صلاحية حسابك ستنتهي خلال {$this->daysLeft} أيام.",
            'days_left' => $this->daysLeft,
            'type' => 'expiration_warning'
        ];
    }
}
