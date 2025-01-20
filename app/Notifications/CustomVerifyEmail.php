<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends BaseVerifyEmail implements ShouldQueue
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
     * @return array
     */
    public function via($notifiable) // Remove `object` type hint to match the base class signature
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('تأكيد بريدك الإلكتروني')
            ->greeting('مرحبًا!')
            ->line('شكرًا لتسجيلك في موقعنا.')
            ->line('لإكمال عملية التسجيل، يرجى تأكيد بريدك الإلكتروني.')
            ->action('تأكيد البريد الإلكتروني', $verificationUrl)
            ->line('إذا لم تقم بإنشاء هذا الحساب، يرجى تجاهل هذه الرسالة.')
            ->salutation('تحياتنا، فريق الدعم');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}

        // ->subject('Reset Your Password')
        //     ->line('You are receiving this email because we received a password reset request for your account.')
        //     ->action('Verify Email', $verificationUrl)
        //     ->line('If you did not request a password reset, no further action is required.')
        //     // ->subject('Verify Your Email Address')
        //     // ->view('emails.verify-email', ['url' => $verificationUrl])
        //     ->view('verify_email', ['url' => $verificationUrl]); 
