<?php

declare(strict_types=1);

namespace App\Modules\Intelligence\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Intelligence\Actions\ForceApproveInstitutionalExceptionAction;
use App\Modules\Intelligence\Actions\GetIntelligenceDashboardAction;
use App\Modules\Intelligence\Actions\ReprocessScheduleInformationAction;
use App\Modules\Intelligence\Actions\ResolveEffectiveScheduleAction;
use App\Modules\Intelligence\Http\Requests\ForceApproveInstitutionalExceptionRequest;
use App\Modules\Intelligence\Http\Requests\ReprocessScheduleInformationRequest;
use App\Modules\Intelligence\Http\Requests\ResolveEffectiveScheduleRequest;
use App\Modules\Workflow\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class IntelligenceController extends Controller {
    public function __construct(
        private GetIntelligenceDashboardAction $getIntelligenceDashboardAction,
        private ResolveEffectiveScheduleAction $resolveEffectiveScheduleAction,
        private ForceApproveInstitutionalExceptionAction $forceApproveInstitutionalExceptionAction,
        private ReprocessScheduleInformationAction $reprocessScheduleInformationAction,
    ) {
    }

    public function index(Request $request): View {
        $user = $request->user();
        abort_if($user === null, 403);

        return view('intelligence::operations.index', $this->getIntelligenceDashboardAction->execute((int) $user->id));
    }

    public function resolveEffectiveSchedule(ResolveEffectiveScheduleRequest $request): RedirectResponse {
        $resolved = $this->resolveEffectiveScheduleAction->execute(
            (int) $request->validated('employee_id'),
            (string) $request->validated('date'),
        );

        return back()
            ->with('status', 'Resolución de horario efectiva ejecutada correctamente.')
            ->with('resolved_schedule', $resolved);
    }

    public function forceApproveInstitutionalException(
        ForceApproveInstitutionalExceptionRequest $request,
        LeaveRequest $leaveRequest,
    ): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->forceApproveInstitutionalExceptionAction->execute(
            $leaveRequest,
            (int) $user->id,
            (string) $request->validated('justification'),
        );

        return back()->with('status', 'Excepción institucional aprobada de forma forzada.');
    }

    public function reprocess(ReprocessScheduleInformationRequest $request): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $resolved = $this->reprocessScheduleInformationAction->execute(
            (int) $user->id,
            (int) $request->validated('employee_id'),
            (string) $request->validated('date'),
        );

        return back()
            ->with('status', 'Reprocesamiento ejecutado correctamente.')
            ->with('resolved_schedule', $resolved);
    }
}
