<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Models\User;
use App\Modules\Security\Notifications\QueuedResetPasswordNotification;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Support\Facades\Password;

final readonly class SendPasswordResetLinkAction {
    public function __construct(
        private PasswordBrokerManager $passwordBrokerManager,
    ) {
    }

    public function execute(string $email): string {
        /** @var User|null $user */
        $user = User::query()
            ->where('email', $email)
            ->where('is_active', true)
            ->first();

        if ($user === null) {
            return Password::RESET_LINK_SENT;
        }

        $this->passwordBrokerManager->broker()->sendResetLink(
            ['email' => $email],
            function (User $user, string $token): void {
                $user->notify(new QueuedResetPasswordNotification($token));
            }
        );

        return Password::RESET_LINK_SENT;
    }
}
