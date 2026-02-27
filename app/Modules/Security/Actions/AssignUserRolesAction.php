<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use Illuminate\Database\DatabaseManager;

final readonly class AssignUserRolesAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<int, int> $roleIds
     */
    public function execute(User $user, array $roleIds): User {
        /** @var User $updatedUser */
        $updatedUser = $this->databaseManager->transaction(function () use ($user, $roleIds): User {
            $roles = Role::query()->whereKey($roleIds)->get();
            $user->syncRoles($roles);

            return $user->refresh();
        });

        return $updatedUser;
    }
}
