<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\WeeklySchedule;
use App\Modules\Schedule\Models\BreakTemplate;
use App\Modules\Schedule\Models\Schedule;
use App\Modules\Team\Models\Team;

final readonly class GetWeeklyPlanningDashboardAction {
    /**
     * @return array{
     *   weeklySchedules:\Illuminate\Database\Eloquent\Collection<int, WeeklySchedule>,
     *   schedules:\Illuminate\Database\Eloquent\Collection<int, Schedule>,
     *   teams:\Illuminate\Database\Eloquent\Collection<int, Team>,
     *   breakTemplates:\Illuminate\Database\Eloquent\Collection<int, BreakTemplate>,
     *   employees:\Illuminate\Database\Eloquent\Collection<int, Employee>
     * }
     */
    public function execute(): array {
        return [
            'weeklySchedules' => WeeklySchedule::query()
                ->with(['assignments.employee.user', 'assignments.schedule', 'assignments.breakTemplate', 'publishedBy'])
                ->orderByDesc('week_start_date')
                ->get(),
            'schedules' => Schedule::query()->where('is_active', true)->orderBy('name')->get(),
            'teams' => Team::query()->where('is_active', true)->orderBy('name')->get(),
            'breakTemplates' => BreakTemplate::query()->where('is_active', true)->orderBy('name')->get(),
            'employees' => Employee::query()->where('is_active', true)->orderBy('first_name')->orderBy('last_name')->get(),
        ];
    }
}
