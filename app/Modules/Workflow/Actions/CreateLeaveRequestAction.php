<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Workflow\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class CreateLeaveRequestAction {
    public function __construct(
        private ValidateLeaveConflictsAction $validateLeaveConflictsAction,
    ) {
    }

    /**
     * @param  array{incident_type_id:int, type:string, start_datetime:string, end_datetime:string, justification:string}  $payload
     * @throws ValidationException
     */
    public function execute(int $userId, array $payload): LeaveRequest {
        /** @var Employee|null $employee */
        $employee = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($employee === null) {
            throw ValidationException::withMessages([
                'employee' => 'No existe empleado activo vinculado al usuario autenticado.',
            ]);
        }

        $this->validateLeaveConflictsAction->execute($employee->id, $payload['start_datetime'], $payload['end_datetime']);

        /** @var LeaveRequest $leaveRequest */
        $leaveRequest = DB::transaction(function () use ($payload, $employee): LeaveRequest {
            return LeaveRequest::query()->create([
                'employee_id' => $employee->id,
                'incident_type_id' => $payload['incident_type_id'],
                'type' => $payload['type'],
                'start_datetime' => $payload['start_datetime'],
                'end_datetime' => $payload['end_datetime'],
                'justification' => $payload['justification'],
                'status' => 'pending',
            ]);
        });

        return $leaveRequest;
    }
}
