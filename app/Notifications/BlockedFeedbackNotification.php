<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlockedFeedbackNotification extends Notification
{
    use Queueable;
protected $feedback;
    /**
     * Create a new notification instance.
     */
    public function __construct($feedback)
    {
        $this->feedback=$feedback;
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
        return (new MailMessage)
        ->subject('Your Feedback Has Been Blocked')
        ->line('Hello,')
        ->line('Your feedback has been blocked due to a violation of our rules.')
        ->line('Feedback: "' . $this->feedback . '"')
        ->line('If you believe this is a mistake, please contact us.')
        ->action('Visit Website', url('/'))
        ->line('Thank you for your understanding.');
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
