<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Welcome ' . $notifiable->name . ' to reuseit')
            ->line('Thank you for using our Website!')
            ->Line('If you have any questions, feel free to contact us at contact@gmail.com.')
            ->salutation('Regards, Reuseit');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'WelcomeMessage' => 'Welcome ' . $notifiable->name . ' to reuseit,
            Thank you for using our Website!,If you have any questions, 
            feel free to contact us at contact@gmail.com.
            Regards, Reuseit'
        ];
    }
}
