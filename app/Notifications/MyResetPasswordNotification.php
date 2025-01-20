<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MyResetPasswordNotification extends Notification
{
    use Queueable;
    protected $token;
   
    
    public function __construct($token)
    {
        
        $this->token=$token;
    }

  
    
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

  
    
    public function toMail($notifiable)
    {
        $resetUrl = url('https://chocolate-eland-808338.hostingersite.com/auth/reset-password') 
        . '&email=' . urlencode($notifiable->email);
        return (new MailMessage)
        ->subject('Reset Your Password')
        ->view('reset_password', ['url' => $resetUrl]);

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