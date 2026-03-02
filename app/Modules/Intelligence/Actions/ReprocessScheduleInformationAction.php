<?php

declare(strict_types=1);

namespace App\Modules\Intelligence\Actions;

use App\Modules\Core\Models\AuditLog;
use App\Modules\Employee\Models\Employee;
use App\Modules\Intelligence\Actions\ResolveEffectiveScheduleAction;

final readonly class ReprocessScheduleInformationAction {
    public function __construct(
        private ResolveEffectiveScheduleAction $resolveEffectiveScheduleAction,
    ) {
    }

    /**
     * @return array{
     *   source:string,
     *   source_label:string,
     *   date:string,
     *   employee_id:int,
     *   details:array<string, mixed>
     * }
     */
    public function execute(int $actorUserId, int $employeeId, string $date): array {
        /** @var Employee|null $actor */
        $actor = Employee::query()
            ->with('user')
            ->where('user_id', $actorUserId)
            ->where('is_active', true)
            ->first();

        abort_if($actor === null || $actor->user === null, 403);
        abort_if(!$actor->user->hasRole('Administrador'), 403);

        /** @var Employee|null $employee */
        $employee = Employee::query()->where('is_active', true)->find($employeeId);
        abort_if($employee === null, 404);

        $resolved = $this->resolveEffectiveScheduleAction->execute((int) $employee->id, $date);

        AuditLog::query()->create([
            'user_id' => (int) $actor->user_id,
            'entity_type' => 'Employee',
            'entity_id' => (int) $employee->id,
            'action' => 'reprocess_executed',
            'before' => null,
            'after' => [
                'date' => $date,
                'resolved' => $resolved,
            ],
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        return $resolved;
    }
}
