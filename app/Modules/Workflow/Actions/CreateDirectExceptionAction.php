<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;
use App\Modules\Workflow\Models\LeaveRequest;
use App\Modules\Workflow\Models\LeaveRequestApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class CreateDirectExceptionAction {
    public function __construct(
        private ValidateLeaveConflictsAction $validateLeaveConflictsAction,
    ) {
    }

    /**
     * @param  array{employee_id:int, incident_type_id:int, type:string, start_datetime:string, end_datetime:string, justification:string}  $payload
     * @throws ValidationException
     */
    public function execute(int $actorUserId, array $payload): LeaveRequest {
        /** @var Employee|null $actor */
        $actor = Employee::query()->with('user')->where('user_id', $actorUserId)->where('is_active', true)->first();

        if ($actor === null || $actor->user === null) {
            throw ValidationException::withMessages([
                'employee_id' => 'No existe empleado activo para el usuario autenticado.',
            ]);
        }

        $isWfm = $actor->user->hasAnyRole(['Administrador', 'Analista WFM']);

        if (!$isWfm) {
            /** @var Team|null $team */
            $team = Team::query()->where('coordinator_employee_id', $actor->id)->where('is_active', true)->first();

            if ($team === null || !$team->activeMembers()->where('employee_id', $payload['employee_id'])->exists()) {
                throw ValidationException::withMessages([
                    'employee_id' => 'El colaborador no pertenece al equipo del coordinador autenticado.',
                ]);
            }
        }

        $this->validateLeaveConflictsAction->execute($payload['employee_id'], $payload['start_datetime'], $payload['end_datetime']);

        /** @var LeaveRequest $leaveRequest */
        $leaveRequest = DB::transaction(function () use ($payload, $actor): LeaveRequest {
            $leaveRequest = LeaveRequest::query()->create([
                'employee_id' => $payload['employee_id'],
                'incident_type_id' => $payload['incident_type_id'],
                'type' => $payload['type'],
                'start_datetime' => $payload['start_datetime'],
                'end_datetime' => $payload['end_datetime'],
                'justification' => $payload['justification'],
                'status' => 'approved',
            ]);

            LeaveRequestApproval::query()->create([
                'leave_request_id' => $leaveRequest->id,
                'approver_id' => $actor->id,
                'step' => 1,
                'action' => 'approved',
                'comments' => 'Excepción directa registrada por actor autorizado.',
                'acted_at' => now(),
            ]);

            return $leaveRequest;
        });

        return $leaveRequest;
    }
}
