<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\EmployeeBreakOverride;
use App\Modules\Planning\Models\IntradayActivityAssignment;
use App\Modules\Planning\Models\WeeklySchedule;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetMyDayTimelineAction {
    /**
     * @return array{
     *   weeklySchedule: WeeklySchedule|null,
     *   assignment: WeeklyScheduleAssignment|null,
     *   intradayAssignments: Collection<int, IntradayActivityAssignment>,
     *   breakOverride: EmployeeBreakOverride|null
     * }
     */
    public function execute(int $userId): array {
        /** @var Employee|null $employee */
        $employee = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($employee === null) {
            return [
                'weeklySchedule' => null,
                'assignment' => null,
                'intradayAssignments' => collect(),
                'breakOverride' => null,
            ];
        }

        $today = CarbonImmutable::today();

        /** @var WeeklySchedule|null $weeklySchedule */
        $weeklySchedule = WeeklySchedule::query()
            ->where('status', 'published')
            ->whereDate('week_start_date', '<=', $today->toDateString())
            ->whereDate('week_end_date', '>=', $today->toDateString())
            ->orderByDesc('week_start_date')
            ->first();

        /** @var WeeklyScheduleAssignment|null $assignment */
        $assignment = $weeklySchedule === null
            ? null
            : WeeklyScheduleAssignment::query()
                ->with(['schedule', 'breakTemplate'])
                ->where('weekly_schedule_id', $weeklySchedule->id)
                ->where('employee_id', $employee->id)
                ->first();

        /** @var Collection<int, IntradayActivityAssignment> $intradayAssignments */
        $intradayAssignments = IntradayActivityAssignment::query()
            ->with('intradayActivity')
            ->where('employee_id', $employee->id)
            ->whereHas('intradayActivity', function ($query) use ($today): void {
                $query->whereDate('activity_date', $today->toDateString());
            })
            ->join('intraday_activities', 'intraday_activities.id', '=', 'intraday_activity_assignments.intraday_activity_id')
            ->orderBy('intraday_activities.start_time')
            ->select('intraday_activity_assignments.*')
            ->get();

        /** @var EmployeeBreakOverride|null $breakOverride */
        $breakOverride = EmployeeBreakOverride::query()
            ->where('employee_id', $employee->id)
            ->whereDate('effective_from', '<=', $today->toDateString())
            ->where(function ($query) use ($today): void {
                $query
                    ->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $today->toDateString());
            })
            ->orderByDesc('effective_from')
            ->first();

        return [
            'weeklySchedule' => $weeklySchedule,
            'assignment' => $assignment,
            'intradayAssignments' => $intradayAssignments,
            'breakOverride' => $breakOverride,
        ];
    }
}
