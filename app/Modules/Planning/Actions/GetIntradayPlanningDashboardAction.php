<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\IntradayActivity;
use App\Modules\Planning\Models\WeeklySchedule;

final readonly class GetIntradayPlanningDashboardAction {
    /**
     * @return array{
     *   weeklySchedules:\Illuminate\Database\Eloquent\Collection<int, WeeklySchedule>,
     *   intradayActivities:\Illuminate\Database\Eloquent\Collection<int, IntradayActivity>,
     *   employees:\Illuminate\Database\Eloquent\Collection<int, Employee>
     * }
     */
    public function execute(): array {
        return [
            'weeklySchedules' => WeeklySchedule::query()
                ->whereIn('status', ['draft', 'published'])
                ->orderByDesc('week_start_date')
                ->get(),
            'intradayActivities' => IntradayActivity::query()
                ->with(['weeklySchedule', 'assignments.employee.user'])
                ->orderByDesc('activity_date')
                ->orderBy('start_time')
                ->get(),
            'employees' => Employee::query()
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
        ];
    }
}
