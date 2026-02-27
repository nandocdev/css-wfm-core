<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Security\Actions\AssignUserRolesAction;
use App\Modules\Security\Actions\CreateUserAccountAction;
use App\Modules\Security\Actions\SyncRolePermissionsAction;
use App\Modules\Security\Actions\ToggleUserStatusAction;
use App\Modules\Security\Actions\UpdateUserAccountAction;
use App\Modules\Security\Http\Requests\AssignUserRolesRequest;
use App\Modules\Security\Http\Requests\StoreUserRequest;
use App\Modules\Security\Http\Requests\SyncRolePermissionsRequest;
use App\Modules\Security\Http\Requests\ToggleUserStatusRequest;
use App\Modules\Security\Http\Requests\UpdateUserRequest;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class UserAdministrationController extends Controller {
    public function __construct(
        private CreateUserAccountAction $createUserAccountAction,
        private UpdateUserAccountAction $updateUserAccountAction,
        private ToggleUserStatusAction $toggleUserStatusAction,
        private AssignUserRolesAction $assignUserRolesAction,
        private SyncRolePermissionsAction $syncRolePermissionsAction,
    ) {
    }

    public function manage(): View {
        return view('security::admin.users.manage');
    }

    public function store(StoreUserRequest $request): JsonResponse {
        $result = $this->createUserAccountAction->execute($request->validated());

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse {
        $updatedUser = $this->updateUserAccountAction->execute($user, $request->validated());

        return response()->json(['user' => $updatedUser], Response::HTTP_OK);
    }

    public function toggleStatus(ToggleUserStatusRequest $request, User $user): JsonResponse {
        $updatedUser = $this->toggleUserStatusAction->execute($user, (bool) $request->validated('is_active'));

        return response()->json(['user' => $updatedUser], Response::HTTP_OK);
    }

    public function assignRoles(AssignUserRolesRequest $request, User $user): JsonResponse {
        $updatedUser = $this->assignUserRolesAction->execute($user, $request->validated('role_ids'));

        return response()->json(['user' => $updatedUser], Response::HTTP_OK);
    }

    public function syncRolePermissions(SyncRolePermissionsRequest $request, Role $role): JsonResponse {
        $updatedRole = $this->syncRolePermissionsAction->execute($role, $request->validated('permission_ids'));

        return response()->json(['role' => $updatedRole], Response::HTTP_OK);
    }
}
