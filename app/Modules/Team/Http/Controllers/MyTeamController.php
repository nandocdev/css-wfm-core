<?php

declare(strict_types=1);

namespace App\Modules\Team\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Employee\Models\Employee;
use App\Modules\Security\Models\User;
use App\Modules\Team\Actions\GetCoordinatorTeamAction;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MyTeamController extends Controller {
    public function __construct(
        private GetCoordinatorTeamAction $getCoordinatorTeamAction,
    ) {
    }

    public function show(Request $request): View {
        /** @var User|null $user */
        $user = $request->user();
        abort_if($user === null || !$user->hasRole('Coordinador'), 403);

        /** @var Employee|null $employee */
        $employee = Employee::query()->where('user_id', (int) $user->id)->first();
        abort_if($employee === null, 403);

        $team = $this->getCoordinatorTeamAction->execute((int) $employee->id);

        return view('team::coordinator.my-team', [
            'team' => $team,
        ]);
    }
}
