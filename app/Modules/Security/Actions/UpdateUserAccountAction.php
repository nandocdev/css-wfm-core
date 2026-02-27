<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Models\User;
use Illuminate\Database\DatabaseManager;

final readonly class UpdateUserAccountAction
{
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array{name:string,email:string} $payload
     */
    public function execute(User $user, array $payload): User
    {
        /** @var User $updatedUser */
        $updatedUser = $this->databaseManager->transaction(function () use ($user, $payload): User {
            $user->update([
                'name' => $payload['name'],
                'email' => $payload['email'],
            ]);

            return $user->refresh();
        });

        return $updatedUser;
    }
}
