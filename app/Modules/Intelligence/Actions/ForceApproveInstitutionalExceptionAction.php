<?php

declare(strict_types=1);

namespace App\Modules\Intelligence\Actions;

use App\Modules\Core\Models\AuditLog;
use App\Modules\Employee\Models\Employee;
use App\Modules\Workflow\Models\LeaveRequest;
use App\Modules\Workflow\Models\LeaveRequestApproval;
use Illuminate\Support\Facades\DB;

final readonly class ForceApproveInstitutionalExceptionAction {
    public function execute(LeaveRequest $leaveRequest, int $actorUserId, string $justification): void {
        /** @var Employee|null $actor */
        $actor = Employee::query()
            ->with('user')
            ->where('user_id', $actorUserId)
            ->where('is_active', true)
            ->first();

        abort_if($actor === null || $actor->user === null, 403);
        abort_if(!$actor->user->hasAnyRole(['Administrador', 'Analista WFM', 'Director', 'Jefe']), 403);
        abort_if($leaveRequest->status === 'approved', 422, 'La solicitud ya está aprobada.');

        DB::transaction(function () use ($leaveRequest, $actor, $justification): void {
            $beforeStatus = (string) $leaveRequest->status;
            $nextStep = (int) ($leaveRequest->approvals()->max('step') ?? 0) + 1;

            $leaveRequest->update([
                'status' => 'approved',
            ]);

            LeaveRequestApproval::query()->create([
                'leave_request_id' => $leaveRequest->id,
                'approver_id' => $actor->id,
                'step' => $nextStep,
                'action' => 'approved',
                'comments' => 'Aprobación forzada institucional. Motivo: ' . $justification,
                'acted_at' => now(),
            ]);

            AuditLog::query()->create([
                'user_id' => (int) $actor->user_id,
                'entity_type' => 'LeaveRequest',
                'entity_id' => (int) $leaveRequest->id,
                'action' => 'force_approved',
                'before' => [
                    'status' => $beforeStatus,
                ],
                'after' => [
                    'status' => 'approved',
                    'forced' => true,
                    'justification' => $justification,
                    'approved_by_employee_id' => (int) $actor->id,
                ],
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);
        });
    }
}
