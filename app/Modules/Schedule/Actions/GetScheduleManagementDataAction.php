<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Actions;

use App\Modules\Planning\Models\WeeklySchedule;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Schedule\Models\BreakTemplate;
use App\Modules\Schedule\Models\Schedule;
use App\Modules\Schedule\Models\WfmSetting;
use App\Modules\Team\Models\Team;

final readonly class GetScheduleManagementDataAction {
    /**
     * @return array{
     *   schedules:\Illuminate\Database\Eloquent\Collection<int, Schedule>,
     *   breakTemplates:\Illuminate\Database\Eloquent\Collection<int, BreakTemplate>,
     *   teams:\Illuminate\Database\Eloquent\Collection<int, Team>,
     *   weeklySchedules:\Illuminate\Database\Eloquent\Collection<int, WeeklySchedule>,
     *   weeklyAssignments:\Illuminate\Database\Eloquent\Collection<int, WeeklyScheduleAssignment>,
     *   wfmSetting: WfmSetting
     * }
     */
    public function execute(): array {
        $wfmSetting = WfmSetting::query()->first() ?? WfmSetting::query()->create([]);

        return [
            'schedules' => Schedule::query()->orderBy('name')->get(),
            'breakTemplates' => BreakTemplate::query()->with('team')->orderBy('name')->get(),
            'teams' => Team::query()->where('is_active', true)->orderBy('name')->get(),
            'weeklySchedules' => WeeklySchedule::query()->orderByDesc('week_start_date')->get(),
            'weeklyAssignments' => WeeklyScheduleAssignment::query()
                ->with(['employee.user', 'weeklySchedule', 'breakTemplate'])
                ->orderByDesc('weekly_schedule_id')
                ->limit(200)
                ->get(),
            'wfmSetting' => $wfmSetting,
        ];
    }
}
