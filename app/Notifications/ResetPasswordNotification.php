<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = rtrim((string) config('app.url'), '/').route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false);

        $data = [
            'appName' => config('app.name', 'DigiPangan Merauke'),
            'userName' => $notifiable->name,
            'resetUrl' => $url,
            'expireMinutes' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60),
            'supportEmail' => config('digipangan.contact_email'),
        ];

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi — '.$data['appName'])
            ->view('emails.password-reset', $data)
            ->text('emails.password-reset-text', $data);
    }
}
