<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Schedule\Actions\AssignBreakTemplateAction;
use App\Modules\Schedule\Actions\CreateBreakTemplateAction;
use App\Modules\Schedule\Actions\CreateScheduleAction;
use App\Modules\Schedule\Actions\DeleteScheduleAction;
use App\Modules\Schedule\Actions\GetScheduleManagementDataAction;
use App\Modules\Schedule\Actions\UpdateBreakTemplateAction;
use App\Modules\Schedule\Actions\UpdateScheduleAction;
use App\Modules\Schedule\Actions\UpsertWfmSettingsAction;
use App\Modules\Schedule\Http\Requests\AssignBreakTemplateRequest;
use App\Modules\Schedule\Http\Requests\StoreBreakTemplateRequest;
use App\Modules\Schedule\Http\Requests\StoreScheduleRequest;
use App\Modules\Schedule\Http\Requests\UpdateBreakTemplateRequest;
use App\Modules\Schedule\Http\Requests\UpdateScheduleRequest;
use App\Modules\Schedule\Http\Requests\UpdateWfmSettingsRequest;
use App\Modules\Schedule\Models\BreakTemplate;
use App\Modules\Schedule\Models\Schedule;
use App\Modules\Security\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ScheduleEngineController extends Controller {
    public function __construct(
        private GetScheduleManagementDataAction $getScheduleManagementDataAction,
        private CreateScheduleAction $createScheduleAction,
        private UpdateScheduleAction $updateScheduleAction,
        private DeleteScheduleAction $deleteScheduleAction,
        private CreateBreakTemplateAction $createBreakTemplateAction,
        private UpdateBreakTemplateAction $updateBreakTemplateAction,
        private AssignBreakTemplateAction $assignBreakTemplateAction,
        private UpsertWfmSettingsAction $upsertWfmSettingsAction,
    ) {
    }

    public function index(Request $request): View {
        $this->assertWfmAccess($request);

        return view('schedule::engine.index', $this->getScheduleManagementDataAction->execute());
    }

    public function storeSchedule(StoreScheduleRequest $request): RedirectResponse {
        $this->createScheduleAction->execute($request->validated());

        return back()->with('status', 'Horario creado correctamente.');
    }

    public function updateSchedule(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse {
        $this->updateScheduleAction->execute($schedule, $request->validated());

        return back()->with('status', 'Horario actualizado correctamente.');
    }

    public function destroySchedule(Request $request, Schedule $schedule): RedirectResponse {
        $this->assertWfmAccess($request);
        $this->deleteScheduleAction->execute($schedule);

        return back()->with('status', 'Horario eliminado correctamente.');
    }

    public function storeBreakTemplate(StoreBreakTemplateRequest $request): RedirectResponse {
        $this->createBreakTemplateAction->execute($request->validated());

        return back()->with('status', 'Plantilla de descansos creada correctamente.');
    }

    public function updateBreakTemplate(UpdateBreakTemplateRequest $request, BreakTemplate $breakTemplate): RedirectResponse {
        $this->updateBreakTemplateAction->execute($breakTemplate, $request->validated());

        return back()->with('status', 'Plantilla de descansos actualizada correctamente.');
    }

    public function assignBreakTemplate(AssignBreakTemplateRequest $request): RedirectResponse {
        $this->assignBreakTemplateAction->execute(
            (int) $request->validated('weekly_schedule_assignment_id'),
            (int) $request->validated('break_template_id'),
        );

        return back()->with('status', 'Plantilla de descansos asignada correctamente.');
    }

    public function updateWfmSettings(UpdateWfmSettingsRequest $request): RedirectResponse {
        $this->upsertWfmSettingsAction->execute($request->validated());

        return back()->with('status', 'Configuraciones WFM actualizadas correctamente.');
    }

    private function assertWfmAccess(Request $request): void {
        /** @var User|null $user */
        $user = $request->user();

        abort_if($user === null || !$user->hasAnyRole(['Administrador', 'Analista WFM']), 403);
    }
}
