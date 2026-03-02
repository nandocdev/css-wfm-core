<?php

declare(strict_types=1);

namespace App\Modules\Team\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Security\Models\User;
use App\Modules\Team\Actions\AddTeamMemberAction;
use App\Modules\Team\Actions\AssignCoordinatorToTeamAction;
use App\Modules\Team\Actions\CreateTeamAction;
use App\Modules\Team\Actions\GetTeamManagementDataAction;
use App\Modules\Team\Http\Requests\AssignCoordinatorRequest;
use App\Modules\Team\Http\Requests\StoreTeamMemberRequest;
use App\Modules\Team\Http\Requests\StoreTeamRequest;
use App\Modules\Team\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TeamManagementController extends Controller {
    public function __construct(
        private GetTeamManagementDataAction $getTeamManagementDataAction,
        private CreateTeamAction $createTeamAction,
        private AddTeamMemberAction $addTeamMemberAction,
        private AssignCoordinatorToTeamAction $assignCoordinatorToTeamAction,
    ) {
    }

    public function manage(Request $request): View {
        /** @var User|null $user */
        $user = $request->user();
        abort_if($user === null || !$user->hasRole('Administrador'), 403);

        return view('team::admin.manage', $this->getTeamManagementDataAction->execute());
    }

    public function storeTeam(StoreTeamRequest $request): RedirectResponse {
        $this->createTeamAction->execute($request->validated());

        return back()->with('status', 'Equipo creado correctamente.');
    }

    public function storeMember(StoreTeamMemberRequest $request): RedirectResponse {
        $this->addTeamMemberAction->execute($request->validated());

        return back()->with('status', 'Miembro asignado correctamente.');
    }

    public function assignCoordinator(AssignCoordinatorRequest $request): RedirectResponse {
        $team = Team::query()->findOrFail((int) $request->validated('team_id'));
        $coordinatorEmployeeId = (int) $request->validated('coordinator_employee_id');

        $this->assignCoordinatorToTeamAction->execute($team, $coordinatorEmployeeId);

        return back()->with('status', 'Coordinador asignado correctamente.');
    }
}
