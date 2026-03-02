<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Actions\EscalateOperationalIncidentAction;
use App\Modules\Attendance\Actions\GetSupervisorEscalationDataAction;
use App\Modules\Attendance\Http\Requests\StoreOperationalEscalationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SupervisorEscalationController extends Controller {
    public function __construct(
        private GetSupervisorEscalationDataAction $getSupervisorEscalationDataAction,
        private EscalateOperationalIncidentAction $escalateOperationalIncidentAction,
    ) {
    }

    public function index(Request $request): View {
        $user = $request->user();
        abort_if($user === null || !$user->hasAnyRole(['Administrador', 'Supervisor', 'Operador II']), 403);

        return view('attendance::supervisor.escalations', $this->getSupervisorEscalationDataAction->execute((int) $user->id));
    }

    public function store(StoreOperationalEscalationRequest $request): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->escalateOperationalIncidentAction->execute((int) $user->id, $request->validated());

        return back()->with('status', 'Escalación operativa enviada al coordinador del equipo.');
    }
}
