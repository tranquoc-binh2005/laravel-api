<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordRequest extends Notification implements ShouldQueue
{
    use Queueable;
    protected $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token = '')
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('v1/auth/reset-password/?token=' . $this->token);
        return (new MailMessage)
                    ->line(Lang::get('mail.welcome'))
                    ->action(Lang::get('mail.notification_action'), url($url))
                    ->line(Lang::get('mail.notion'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
