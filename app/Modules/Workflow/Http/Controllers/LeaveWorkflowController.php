<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Workflow\Actions\ApproveLeaveRequestAction;
use App\Modules\Workflow\Actions\CreateBulkExceptionsAction;
use App\Modules\Workflow\Actions\CreateDirectExceptionAction;
use App\Modules\Workflow\Actions\CreateLeaveRequestAction;
use App\Modules\Workflow\Actions\CreateShiftSwapRequestAction;
use App\Modules\Workflow\Actions\GetWorkflowDashboardAction;
use App\Modules\Workflow\Actions\RespondShiftSwapRequestAction;
use App\Modules\Workflow\Actions\RejectLeaveRequestAction;
use App\Modules\Workflow\Actions\ReviewShiftSwapRequestAction;
use App\Modules\Workflow\Http\Requests\ApproveLeaveRequest;
use App\Modules\Workflow\Http\Requests\RespondShiftSwapRequest;
use App\Modules\Workflow\Http\Requests\ReviewShiftSwapRequest;
use App\Modules\Workflow\Http\Requests\RejectLeaveRequest;
use App\Modules\Workflow\Http\Requests\StoreBulkExceptionRequest;
use App\Modules\Workflow\Http\Requests\StoreDirectExceptionRequest;
use App\Modules\Workflow\Http\Requests\StoreLeaveRequest;
use App\Modules\Workflow\Http\Requests\StoreShiftSwapRequest;
use App\Modules\Workflow\Models\LeaveRequest;
use App\Modules\Workflow\Models\ShiftSwapRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LeaveWorkflowController extends Controller {
    public function __construct(
        private GetWorkflowDashboardAction $getWorkflowDashboardAction,
        private CreateLeaveRequestAction $createLeaveRequestAction,
        private ApproveLeaveRequestAction $approveLeaveRequestAction,
        private RejectLeaveRequestAction $rejectLeaveRequestAction,
        private CreateShiftSwapRequestAction $createShiftSwapRequestAction,
        private RespondShiftSwapRequestAction $respondShiftSwapRequestAction,
        private ReviewShiftSwapRequestAction $reviewShiftSwapRequestAction,
        private CreateDirectExceptionAction $createDirectExceptionAction,
        private CreateBulkExceptionsAction $createBulkExceptionsAction,
    ) {
    }

    public function index(Request $request): View {
        $user = $request->user();
        abort_if($user === null, 403);

        return view('workflow::leave.index', $this->getWorkflowDashboardAction->execute((int) $user->id));
    }

    public function store(StoreLeaveRequest $request): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->createLeaveRequestAction->execute((int) $user->id, $request->validated());

        return back()->with('status', 'Solicitud de permiso registrada correctamente.');
    }

    public function approve(ApproveLeaveRequest $request, LeaveRequest $leaveRequest): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->approveLeaveRequestAction->execute($leaveRequest, (int) $user->id, $request->validated('comments'));

        return back()->with('status', 'Solicitud aprobada correctamente.');
    }

    public function reject(RejectLeaveRequest $request, LeaveRequest $leaveRequest): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->rejectLeaveRequestAction->execute($leaveRequest, (int) $user->id, (string) $request->validated('comments'));

        return back()->with('status', 'Solicitud rechazada correctamente.');
    }

    public function storeShiftSwap(StoreShiftSwapRequest $request): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->createShiftSwapRequestAction->execute((int) $user->id, $request->validated());

        return back()->with('status', 'Solicitud de cambio de turno registrada correctamente.');
    }

    public function respondShiftSwap(RespondShiftSwapRequest $request, ShiftSwapRequest $shiftSwapRequest): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->respondShiftSwapRequestAction->execute(
            $shiftSwapRequest,
            (int) $user->id,
            (string) $request->validated('action'),
            $request->validated('comments'),
        );

        return back()->with('status', 'Respuesta de cambio de turno registrada correctamente.');
    }

    public function reviewShiftSwap(ReviewShiftSwapRequest $request, ShiftSwapRequest $shiftSwapRequest): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->reviewShiftSwapRequestAction->execute(
            $shiftSwapRequest,
            (int) $user->id,
            (string) $request->validated('action'),
            $request->validated('comments'),
        );

        return back()->with('status', 'Revisión de cambio de turno registrada correctamente.');
    }

    public function storeDirectException(StoreDirectExceptionRequest $request): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->createDirectExceptionAction->execute((int) $user->id, $request->validated());

        return back()->with('status', 'Excepción individual registrada correctamente.');
    }

    public function storeBulkExceptions(StoreBulkExceptionRequest $request): RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $count = $this->createBulkExceptionsAction->execute((int) $user->id, $request->validated());

        return back()->with('status', "Excepciones masivas registradas: {$count} empleados.");
    }
}
