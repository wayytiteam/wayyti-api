<?php

namespace App\Notifications;

use App\Broadcasting\FcmNotificationSent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificationSent extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $title;
    public $message;
    public $fcm_token;
    public $notification;

    public function __construct($title, $message, $fcm_token, $notification)
    {
        $this->title = $title;
        $this->message = $message;
        $this->fcm_token = $fcm_token;
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return [FcmNotificationSent::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         //
    //     ];
    // }
    public function locale($locale)
    {
        return [
            'message' => [
                'topic' => $this->fcm_token,
                'notification' => [
                    'title' => $this->title,
                    'body' => $this->message
                ],
            ],
        ];
    }
}
