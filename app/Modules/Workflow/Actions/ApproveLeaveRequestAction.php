<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Contracts\Core\NotificationDispatcherContract;
use App\Modules\Employee\Models\Employee;
use App\Modules\Workflow\Models\LeaveRequest;
use App\Modules\Workflow\Models\LeaveRequestApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class ApproveLeaveRequestAction {
    public function __construct(
        private ResolveLeaveApproverAction $resolveLeaveApproverAction,
        private ValidateLeaveConflictsAction $validateLeaveConflictsAction,
        private NotificationDispatcherContract $notificationDispatcher,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function execute(LeaveRequest $leaveRequest, int $approverUserId, ?string $comments): void {
        if ($leaveRequest->status !== 'pending') {
            throw ValidationException::withMessages([
                'leave_request' => 'Solo las solicitudes pendientes pueden aprobarse.',
            ]);
        }

        /** @var Employee|null $actor */
        $actor = Employee::query()->where('user_id', $approverUserId)->where('is_active', true)->first();

        if ($actor === null) {
            throw ValidationException::withMessages([
                'leave_request' => 'No existe empleado activo para el aprobador autenticado.',
            ]);
        }

        $leaveRequest->loadMissing('incidentType');
        $expectedApprover = $this->resolveLeaveApproverAction->execute($leaveRequest);

        if ((int) $expectedApprover->id !== (int) $actor->id && !$actor->user?->hasAnyRole(['Administrador', 'Analista WFM'])) {
            throw ValidationException::withMessages([
                'leave_request' => 'No tienes autorización jerárquica para aprobar esta solicitud.',
            ]);
        }

        if ($leaveRequest->approvals()->exists()) {
            throw ValidationException::withMessages([
                'leave_request' => 'La solicitud ya tiene un registro de aprobación procesado (flujo de un paso).',
            ]);
        }

        $this->validateLeaveConflictsAction->execute(
            (int) $leaveRequest->employee_id,
            (string) $leaveRequest->start_datetime,
            (string) $leaveRequest->end_datetime,
            (int) $leaveRequest->id,
        );

        DB::transaction(function () use ($leaveRequest, $actor, $comments): void {
            LeaveRequestApproval::query()->create([
                'leave_request_id' => $leaveRequest->id,
                'approver_id' => $actor->id,
                'step' => 1,
                'action' => 'approved',
                'comments' => $comments,
                'acted_at' => now(),
            ]);

            $leaveRequest->status = 'approved';
            $leaveRequest->save();
        });

        $leaveRequest->loadMissing('employee.user');

        $targetUser = $leaveRequest->employee?->user;

        if ($targetUser !== null) {
            $this->notificationDispatcher->dispatch(
                [$targetUser],
                'Permiso aprobado',
                'Tu solicitud de permiso fue aprobada.',
                '/workflow/leaves',
            );
        }
    }
}
