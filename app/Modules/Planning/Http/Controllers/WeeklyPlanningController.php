<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Planning\Actions\CreateWeeklyScheduleAction;
use App\Modules\Planning\Actions\GetWeeklyPlanningDashboardAction;
use App\Modules\Planning\Actions\MassAssignWeeklyScheduleAction;
use App\Modules\Planning\Actions\PublishWeeklyScheduleAction;
use App\Modules\Planning\Http\Requests\MassAssignWeeklyScheduleRequest;
use App\Modules\Planning\Http\Requests\StoreWeeklyScheduleRequest;
use App\Modules\Planning\Models\WeeklySchedule;
use App\Modules\Security\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class WeeklyPlanningController extends Controller {
    public function __construct(
        private GetWeeklyPlanningDashboardAction $getWeeklyPlanningDashboardAction,
        private CreateWeeklyScheduleAction $createWeeklyScheduleAction,
        private MassAssignWeeklyScheduleAction $massAssignWeeklyScheduleAction,
        private PublishWeeklyScheduleAction $publishWeeklyScheduleAction,
    ) {
    }

    public function index(Request $request): View {
        $this->assertWfmAccess($request);

        return view('planning::weekly.index', $this->getWeeklyPlanningDashboardAction->execute());
    }

    public function store(StoreWeeklyScheduleRequest $request): RedirectResponse {
        $this->createWeeklyScheduleAction->execute((string) $request->validated('week_start_date'));

        return back()->with('status', 'Planificación semanal draft creada correctamente.');
    }

    public function massAssign(MassAssignWeeklyScheduleRequest $request): RedirectResponse {
        $weeklySchedule = WeeklySchedule::query()->findOrFail((int) $request->validated('weekly_schedule_id'));
        $payload = $request->safe()->except(['weekly_schedule_id']);

        $assignedCount = $this->massAssignWeeklyScheduleAction->execute($weeklySchedule, $payload);

        return back()->with('status', "Asignación masiva completada: {$assignedCount} empleados afectados.");
    }

    public function publish(Request $request, WeeklySchedule $weeklySchedule): RedirectResponse {
        /** @var User|null $user */
        $user = $request->user();
        abort_if($user === null || !$user->hasAnyRole(['Administrador', 'Analista WFM']), 403);

        $this->publishWeeklyScheduleAction->execute($weeklySchedule, (int) $user->id);

        return back()->with('status', 'Planificación semanal publicada correctamente.');
    }

    private function assertWfmAccess(Request $request): void {
        /** @var User|null $user */
        $user = $request->user();

        abort_if($user === null || !$user->hasAnyRole(['Administrador', 'Analista WFM']), 403);
    }
}
