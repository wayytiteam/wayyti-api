<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OTPSent extends Notification
{
    use Queueable;

    public $code;
    /**
     * Create a new notification instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
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
        $code = $this->code;
        return (new MailMessage)
            ->line('We received a request to reset your Wayyti password. To continue, enter the code below in the app:')
            ->line($code)
            ->line('This code is valid for the next 24 hours.')
            ->line('Tracking made simple: keep adding products to your list, and we’ll handle the rest—it’s as simple as: search, track, wait, and save!')
            ->line('Thank you')
            ->salutation('The Wayyti Team');
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
