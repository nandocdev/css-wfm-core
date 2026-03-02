<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\WeeklySchedule;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Team\Models\TeamMember;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class MassAssignWeeklyScheduleAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(WeeklySchedule $weeklySchedule, array $payload): int {
        if ($weeklySchedule->status !== 'draft') {
            throw ValidationException::withMessages([
                'weekly_schedule' => 'Solo se permiten asignaciones masivas en planificación draft.',
            ]);
        }

        $employeeIds = $this->resolveEmployees($payload);

        if ($employeeIds === []) {
            throw ValidationException::withMessages([
                'employee_ids' => 'No se encontraron empleados válidos para asignar.',
            ]);
        }

        return $this->databaseManager->transaction(function () use ($weeklySchedule, $payload, $employeeIds): int {
            foreach ($employeeIds as $employeeId) {
                WeeklyScheduleAssignment::query()->updateOrCreate(
                    [
                        'weekly_schedule_id' => $weeklySchedule->id,
                        'employee_id' => $employeeId,
                    ],
                    [
                        'schedule_id' => (int) $payload['schedule_id'],
                        'break_template_id' => isset($payload['break_template_id']) && $payload['break_template_id'] !== null ? (int) $payload['break_template_id'] : null,
                        'is_custom_break' => false,
                    ],
                );
            }

            return count($employeeIds);
        });
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<int, int>
     */
    private function resolveEmployees(array $payload): array {
        $employees = [];

        if (isset($payload['team_id']) && is_numeric($payload['team_id'])) {
            $teamEmployees = TeamMember::query()
                ->active()
                ->where('team_id', (int) $payload['team_id'])
                ->pluck('employee_id')
                ->map(static fn($id): int => (int) $id)
                ->all();

            $employees = array_merge($employees, $teamEmployees);
        }

        if (isset($payload['employee_ids']) && is_array($payload['employee_ids'])) {
            foreach ($payload['employee_ids'] as $employeeId) {
                if (is_numeric($employeeId)) {
                    $employees[] = (int) $employeeId;
                }
            }
        }

        $employees = array_values(array_unique($employees));

        if ($employees === []) {
            return [];
        }

        return Employee::query()
            ->whereIn('id', $employees)
            ->where('is_active', true)
            ->pluck('id')
            ->map(static fn($id): int => (int) $id)
            ->all();
    }
}
