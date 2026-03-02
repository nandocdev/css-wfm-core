<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\IntradayActivity;
use App\Modules\Planning\Models\IntradayActivityAssignment;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class AssignEmployeesToIntradayActivityAction {
    /**
     * @param  array<int, int>  $employeeIds
     * @throws ValidationException
     */
    public function execute(IntradayActivity $activity, array $employeeIds): int {
        $targetEmployeeIds = array_values(array_unique(array_map('intval', $employeeIds)));

        if ($targetEmployeeIds === []) {
            return 0;
        }

        $eligibleEmployeeIds = WeeklyScheduleAssignment::query()
            ->where('weekly_schedule_id', $activity->weekly_schedule_id)
            ->whereIn('employee_id', $targetEmployeeIds)
            ->pluck('employee_id')
            ->map(static fn($id): int => (int) $id)
            ->all();

        $ineligibleEmployeeIds = array_values(array_diff($targetEmployeeIds, $eligibleEmployeeIds));

        if ($ineligibleEmployeeIds !== []) {
            throw ValidationException::withMessages([
                'employee_ids' => 'Algunos empleados no tienen asignación semanal vigente para esta actividad.',
            ]);
        }

        $alreadyAssignedIds = IntradayActivityAssignment::query()
            ->where('intraday_activity_id', $activity->id)
            ->whereIn('employee_id', $targetEmployeeIds)
            ->pluck('employee_id')
            ->map(static fn($id): int => (int) $id)
            ->all();

        $newEmployeeIds = array_values(array_diff($targetEmployeeIds, $alreadyAssignedIds));

        $conflictingEmployeeIds = IntradayActivityAssignment::query()
            ->whereIn('employee_id', $newEmployeeIds)
            ->whereHas('intradayActivity', function ($query) use ($activity): void {
                $query
                    ->whereKeyNot($activity->id)
                    ->whereDate('activity_date', (string) $activity->activity_date)
                    ->where('start_time', '<', (string) $activity->end_time)
                    ->where('end_time', '>', (string) $activity->start_time);
            })
            ->pluck('employee_id')
            ->map(static fn($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($conflictingEmployeeIds !== []) {
            $conflictingNames = Employee::query()
                ->whereIn('id', $conflictingEmployeeIds)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
                ->map(static fn(Employee $employee): string => trim("{$employee->first_name} {$employee->last_name}"))
                ->implode(', ');

            throw ValidationException::withMessages([
                'employee_ids' => "Existen conflictos de horario para: {$conflictingNames}.",
            ]);
        }

        $currentCount = IntradayActivityAssignment::query()
            ->where('intraday_activity_id', $activity->id)
            ->count();

        if ($activity->max_participants !== null && ($currentCount + count($newEmployeeIds)) > $activity->max_participants) {
            throw ValidationException::withMessages([
                'employee_ids' => 'La asignación excede el cupo máximo configurado para la actividad.',
            ]);
        }

        if ($newEmployeeIds === []) {
            return 0;
        }

        $insertedCount = DB::transaction(function () use ($activity, $newEmployeeIds): int {
            $now = now();

            $rows = array_map(
                static fn(int $employeeId): array => [
                    'intraday_activity_id' => $activity->id,
                    'employee_id' => $employeeId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $newEmployeeIds,
            );

            IntradayActivityAssignment::query()->insert($rows);

            return count($rows);
        });

        return $insertedCount;
    }
}
