<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingNotifications extends Notification
{
    use Queueable;
    private $type;
    private $message;
    private $title;
    private $created_at;

    /**
     * Create a new notification instance.
     */
    public function __construct($type , $message , $title, $created_at)
    {
        $this->type       = $type;
        $this->message    = $message;
        $this->title      = $title;
        $this->created_at = $created_at;
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

    public function toDatabase($notifiable)
    {
        return [
            'type'       => $this->type,
            'message'    => $this->message,
            'title'      => $this->title,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
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
    public function toArray(object $notifiable): array
    {
        return [
            'type'       => $this->type,
            'message'    => $this->message,
            'title'      => $this->title,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
