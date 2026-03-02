<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\WeeklySchedule;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use Carbon\CarbonImmutable;

final readonly class GetMyCurrentScheduleAction {
    /**
     * @return array{weeklySchedule: WeeklySchedule|null, assignment: WeeklyScheduleAssignment|null}
     */
    public function execute(int $userId): array {
        /** @var Employee|null $employee */
        $employee = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($employee === null) {
            return ['weeklySchedule' => null, 'assignment' => null];
        }

        $today = CarbonImmutable::today();

        /** @var WeeklySchedule|null $weeklySchedule */
        $weeklySchedule = WeeklySchedule::query()
            ->where('status', 'published')
            ->whereDate('week_start_date', '<=', $today->toDateString())
            ->whereDate('week_end_date', '>=', $today->toDateString())
            ->orderByDesc('week_start_date')
            ->first();

        if ($weeklySchedule === null) {
            return ['weeklySchedule' => null, 'assignment' => null];
        }

        /** @var WeeklyScheduleAssignment|null $assignment */
        $assignment = WeeklyScheduleAssignment::query()
            ->with(['schedule', 'breakTemplate', 'weeklySchedule'])
            ->where('weekly_schedule_id', $weeklySchedule->id)
            ->where('employee_id', $employee->id)
            ->first();

        return [
            'weeklySchedule' => $weeklySchedule,
            'assignment' => $assignment,
        ];
    }
}
