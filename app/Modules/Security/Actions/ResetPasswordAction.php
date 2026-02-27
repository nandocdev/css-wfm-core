<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Models\User;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

final readonly class ResetPasswordAction
{
    public function __construct(
        private PasswordBrokerManager $passwordBrokerManager,
        private Hasher $hasher,
        private DatabaseManager $databaseManager,
    ) {
    }

    public function execute(string $email, string $token, string $plainPassword): string
    {
        return $this->passwordBrokerManager->broker()->reset(
            [
                'email' => $email,
                'token' => $token,
                'password' => $plainPassword,
                'password_confirmation' => $plainPassword,
            ],
            function (User $user, string $newPassword): void {
                $this->databaseManager->transaction(function () use ($user, $newPassword): void {
                    $user->forceFill([
                        'password' => $this->hasher->make($newPassword),
                        'force_password_change' => false,
                        'remember_token' => Str::random(60),
                    ])->save();
                });
            }
        );
    }
}
