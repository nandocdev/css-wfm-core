<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Actions\CreateEmployeeBreakOverrideAction;
use App\Modules\Planning\Actions\GetCoordinatorBreakManagementDataAction;
use App\Modules\Planning\Http\Requests\StoreEmployeeBreakOverrideRequest;
use App\Modules\Security\Models\User;
use App\Modules\Team\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CoordinatorBreakController extends Controller {
    public function __construct(
        private GetCoordinatorBreakManagementDataAction $getCoordinatorBreakManagementDataAction,
        private CreateEmployeeBreakOverrideAction $createEmployeeBreakOverrideAction,
    ) {
    }

    public function index(Request $request): View {
        /** @var User|null $user */
        $user = $request->user();
        abort_if($user === null || !$user->hasAnyRole(['Administrador', 'Coordinador']), 403);

        return view('planning::coordinator.breaks', $this->getCoordinatorBreakManagementDataAction->execute((int) $user->id));
    }

    public function store(StoreEmployeeBreakOverrideRequest $request): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        $this->assertEmployeeInCoordinatorTeam((int) $user->id, (int) $request->validated('employee_id'));
        $this->createEmployeeBreakOverrideAction->execute($request->validated(), (int) $user->id);

        return back()->with('status', 'Sobrescritura de pausas registrada correctamente.');
    }

    private function assertEmployeeInCoordinatorTeam(int $userId, int $employeeId): void {
        /** @var Employee|null $coordinator */
        $coordinator = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();
        abort_if($coordinator === null, 403);

        /** @var Team|null $team */
        $team = Team::query()->where('coordinator_employee_id', $coordinator->id)->where('is_active', true)->first();
        abort_if($team === null, 403);

        $belongsToTeam = $team->activeMembers()->where('employee_id', $employeeId)->exists();
        abort_if(!$belongsToTeam, 403);
    }
}
