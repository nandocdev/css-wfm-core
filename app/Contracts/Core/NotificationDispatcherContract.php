<?php

declare(strict_types=1);

namespace App\Contracts\Core;

interface NotificationDispatcherContract {
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
    ): void;
}
