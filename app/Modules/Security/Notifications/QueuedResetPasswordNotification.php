<?php

declare(strict_types=1);

namespace App\Modules\Security\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class QueuedResetPasswordNotification extends Notification implements ShouldQueue {
    use Queueable;

    public function __construct(private readonly string $token) {
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage {
        $resetUrl = route('security.auth.password.reset.form', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage())
            ->subject('Recuperación de contraseña')
            ->line('Recibimos una solicitud para restablecer tu contraseña.')
            ->action('Restablecer contraseña', $resetUrl)
            ->line('Si no solicitaste este cambio, ignora este mensaje.');
    }
}
