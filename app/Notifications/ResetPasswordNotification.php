<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\Notifications\ResetPassword;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // The password reset token.
    public $token;

    // Create a new notification instance.
    public function __construct($token)
    {
        $this->token = $token;
    }

    // Get the notification's delivery channels.
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    // Get the mail representation of the notification.
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action(Lang::get('Reset Password'), $resetUrl)
            ->line('This password reset link will expire in ' . config('auth.passwords.users.expire') . ' minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('Regards, Stock Flow Team');
    }

    // Get the array representation of the notification.
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
