<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Workflow\Models\LeaveRequest;
use App\Modules\Workflow\Models\LeaveRequestApproval;
use Illuminate\Support\Facades\DB;

final readonly class CreateBulkExceptionsAction {
    public function __construct(
        private ValidateLeaveConflictsAction $validateLeaveConflictsAction,
    ) {
    }

    /**
     * @param  array{employee_ids:array<int,int>, incident_type_id:int, type:string, start_datetime:string, end_datetime:string, justification:string}  $payload
     */
    public function execute(int $actorUserId, array $payload): int {
        /** @var Employee|null $actor */
        $actor = Employee::query()->with('user')->where('user_id', $actorUserId)->where('is_active', true)->first();

        abort_if($actor === null || $actor->user === null || !$actor->user->hasAnyRole(['Administrador', 'Analista WFM']), 403);

        $employeeIds = array_values(array_unique(array_map('intval', $payload['employee_ids'])));

        if ($employeeIds === []) {
            return 0;
        }

        foreach ($employeeIds as $employeeId) {
            $this->validateLeaveConflictsAction->execute($employeeId, $payload['start_datetime'], $payload['end_datetime']);
        }

        return DB::transaction(function () use ($payload, $employeeIds, $actor): int {
            $created = 0;

            foreach ($employeeIds as $employeeId) {
                $leaveRequest = LeaveRequest::query()->create([
                    'employee_id' => $employeeId,
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
                    'comments' => 'Excepción masiva aprobada por WFM.',
                    'acted_at' => now(),
                ]);

                $created++;
            }

            return $created;
        });
    }
}
