<?php

declare(strict_types=1);

namespace App\Modules\Core\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SystemAlertNotification extends Notification {
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        private string $title,
        private string $message,
        private ?string $url = null,
        private array $context = [],
        private ?string $mailSubject = null,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage {
        $mailMessage = (new MailMessage())
            ->subject($this->mailSubject ?? $this->title)
            ->line($this->message);

        if ($this->url !== null) {
            $mailMessage->action('Ver detalle', $this->url);
        }

        return $mailMessage;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'context' => $this->context,
        ];
    }
}
