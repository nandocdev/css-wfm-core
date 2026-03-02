<?php

declare(strict_types=1);

namespace App\Modules\Core\Actions;

use App\Contracts\Core\NotificationDispatcherContract;
use App\Modules\Core\Notifications\SystemAlertNotification;
use Illuminate\Contracts\Notifications\Dispatcher;

final readonly class DispatchSystemNotificationAction implements NotificationDispatcherContract {
    public function __construct(
        private Dispatcher $dispatcher,
    ) {
    }

    /**
     * @param iterable<mixed> $notifiables
     * @param array<string, mixed> $context
     */
    public function dispatch(
        iterable $notifiables,
        string $title,
        string $message,
        ?string $url = null,
        array $context = [],
        ?string $mailSubject = null,
    ): void {
        $this->dispatcher->send(
            $notifiables,
            new SystemAlertNotification($title, $message, $url, $context, $mailSubject),
        );
    }
}
