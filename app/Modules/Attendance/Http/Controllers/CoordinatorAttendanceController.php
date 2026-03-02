<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Attendance\Actions\GetCoordinatorAttendanceDashboardAction;
use App\Modules\Attendance\Actions\RegisterAttendanceIncidentAction;
use App\Modules\Attendance\Http\Requests\StoreAttendanceIncidentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class CoordinatorAttendanceController extends Controller {
    public function __construct(
        private GetCoordinatorAttendanceDashboardAction $getCoordinatorAttendanceDashboardAction,
        private RegisterAttendanceIncidentAction $registerAttendanceIncidentAction,
    ) {
    }

    public function index(Request $request): View {
        $user = $request->user();
        abort_if($user === null || !$user->hasAnyRole(['Administrador', 'Coordinador']), 403);

        return view('attendance::coordinator.incidents', $this->getCoordinatorAttendanceDashboardAction->execute((int) $user->id));
    }

    public function store(StoreAttendanceIncidentRequest $request): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->registerAttendanceIncidentAction->execute($request->validated(), (int) $user->id);

        return back()->with('status', 'Incidencia de asistencia registrada correctamente.');
    }
}
