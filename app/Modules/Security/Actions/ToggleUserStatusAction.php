<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Models\User;
use Illuminate\Database\DatabaseManager;

final readonly class ToggleUserStatusAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    public function execute(User $user, bool $isActive): User {
        /** @var User $updatedUser */
        $updatedUser = $this->databaseManager->transaction(function () use ($user, $isActive): User {
            $user->forceFill(['is_active' => $isActive])->save();

            return $user->refresh();
        });

        return $updatedUser;
    }
}
