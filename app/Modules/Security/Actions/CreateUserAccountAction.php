<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;

final readonly class CreateUserAccountAction
{
    public function __construct(
        private Hasher $hasher,
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array{name:string,email:string,is_active?:bool} $payload
     * @return array{user:User,temporary_password:string}
     */
    public function execute(array $payload): array
    {
        $temporaryPassword = Str::password(16, true, true, true, false);

        /** @var User $user */
        $user = $this->databaseManager->transaction(function () use ($payload, $temporaryPassword): User {
            return User::query()->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'password' => $this->hasher->make($temporaryPassword),
                'is_active' => $payload['is_active'] ?? true,
                'force_password_change' => true,
            ]);
        });

        return [
            'user' => $user,
            'temporary_password' => $temporaryPassword,
        ];
    }
}
