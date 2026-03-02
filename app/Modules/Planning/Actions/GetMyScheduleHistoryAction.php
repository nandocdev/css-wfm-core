<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use Carbon\CarbonImmutable;

final readonly class GetMyScheduleHistoryAction {
    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, WeeklyScheduleAssignment>
     */
    public function execute(int $userId) {
        /** @var Employee|null $employee */
        $employee = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($employee === null) {
            return WeeklyScheduleAssignment::query()->whereRaw('1 = 0')->get();
        }

        $today = CarbonImmutable::today()->toDateString();

        return WeeklyScheduleAssignment::query()
            ->with(['schedule', 'breakTemplate', 'weeklySchedule'])
            ->where('employee_id', $employee->id)
            ->whereHas('weeklySchedule', static function ($query) use ($today): void {
                $query->where('status', 'published')->whereDate('week_end_date', '<', $today);
            })
            ->orderByDesc('weekly_schedule_id')
            ->limit(52)
            ->get();
    }
}
