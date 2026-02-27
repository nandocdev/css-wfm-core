<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Models\Permission;
use App\Modules\Security\Models\Role;
use Illuminate\Database\DatabaseManager;

final readonly class SyncRolePermissionsAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<int, int> $permissionIds
     */
    public function execute(Role $role, array $permissionIds): Role {
        /** @var Role $updatedRole */
        $updatedRole = $this->databaseManager->transaction(function () use ($role, $permissionIds): Role {
            $permissions = Permission::query()->whereKey($permissionIds)->get();
            $role->syncPermissions($permissions);

            return $role->refresh();
        });

        return $updatedRole;
    }
}
