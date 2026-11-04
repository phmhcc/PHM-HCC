<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountInvitation extends Notification
{
    use Queueable;

    public function __construct(public readonly string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Welcome to PHM HCC Church Website – Activate Your Account')
            ->view('emails.account-invitation', [
                'logoUrl' => asset('Images/images.jpg'),
                'userName' => $notifiable->name,
                'userEmail' => $notifiable->getEmailForPasswordReset(),
                'userDepartment' => $notifiable->department?->name ?? 'Not assigned',
                'userRole' => str($notifiable->role)->replace('_', ' ')->title()->toString(),
                'resetUrl' => $resetUrl,
            ]);
    }
}
