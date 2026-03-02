<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Planning\Actions\AssignEmployeesToIntradayActivityAction;
use App\Modules\Planning\Actions\CreateIntradayActivityAction;
use App\Modules\Planning\Actions\GetIntradayPlanningDashboardAction;
use App\Modules\Planning\Http\Requests\AssignIntradayActivityRequest;
use App\Modules\Planning\Http\Requests\StoreIntradayActivityRequest;
use App\Modules\Planning\Models\IntradayActivity;
use App\Modules\Security\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class IntradayPlanningController extends Controller {
    public function __construct(
        private GetIntradayPlanningDashboardAction $getIntradayPlanningDashboardAction,
        private CreateIntradayActivityAction $createIntradayActivityAction,
        private AssignEmployeesToIntradayActivityAction $assignEmployeesToIntradayActivityAction,
    ) {
    }

    public function index(Request $request): View {
        $this->assertWfmAccess($request);

        return view('planning::intraday.index', $this->getIntradayPlanningDashboardAction->execute());
    }

    public function store(StoreIntradayActivityRequest $request): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        $this->createIntradayActivityAction->execute($request->validated(), (int) $user->id);

        return back()->with('status', 'Actividad intradía creada correctamente.');
    }

    public function assign(AssignIntradayActivityRequest $request): RedirectResponse {
        $intradayActivity = IntradayActivity::query()->findOrFail((int) $request->validated('intraday_activity_id'));
        $employeeIds = $request->validated('employee_ids', []);
        $assignedCount = $this->assignEmployeesToIntradayActivityAction->execute($intradayActivity, $employeeIds);

        return back()->with('status', "Asignación completada: {$assignedCount} operadores agregados.");
    }

    private function assertWfmAccess(Request $request): void {
        /** @var User|null $user */
        $user = $request->user();

        abort_if($user === null || !$user->hasAnyRole(['Administrador', 'Analista WFM']), 403);
    }
}
