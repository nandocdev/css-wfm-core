<?php

declare(strict_types=1);

namespace App\Modules\Intelligence\Actions;

use App\Modules\Planning\Models\IntradayActivityAssignment;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Workflow\Models\LeaveRequest;
use Carbon\CarbonImmutable;

final readonly class ResolveEffectiveScheduleAction {
    /**
     * @return array{
     *   source:string,
     *   source_label:string,
     *   date:string,
     *   employee_id:int,
     *   details:array<string, mixed>
     * }
     */
    public function execute(int $employeeId, string $date): array {
        $workDate = CarbonImmutable::parse($date)->startOfDay();
        $startOfDay = $workDate->startOfDay();
        $endOfDay = $workDate->endOfDay();

        /** @var LeaveRequest|null $approvedException */
        $approvedException = LeaveRequest::query()
            ->with('incidentType')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('start_datetime', '<=', $endOfDay)
            ->where('end_datetime', '>=', $startOfDay)
            ->orderByDesc('start_datetime')
            ->first();

        if ($approvedException !== null) {
            return [
                'source' => 'exception',
                'source_label' => 'Excepción aprobada',
                'date' => $workDate->toDateString(),
                'employee_id' => $employeeId,
                'details' => [
                    'leave_request_id' => (int) $approvedException->id,
                    'incident_type' => $approvedException->incidentType?->name,
                    'start_datetime' => (string) $approvedException->start_datetime,
                    'end_datetime' => (string) $approvedException->end_datetime,
                    'justification' => $approvedException->justification,
                ],
            ];
        }

        /** @var IntradayActivityAssignment|null $intradayAssignment */
        $intradayAssignment = IntradayActivityAssignment::query()
            ->select('intraday_activity_assignments.*')
            ->join('intraday_activities', 'intraday_activities.id', '=', 'intraday_activity_assignments.intraday_activity_id')
            ->with('intradayActivity.weeklySchedule')
            ->where('intraday_activity_assignments.employee_id', $employeeId)
            ->whereDate('intraday_activities.activity_date', $workDate->toDateString())
            ->orderBy('intraday_activities.start_time')
            ->first();

        if ($intradayAssignment !== null && $intradayAssignment->intradayActivity !== null) {
            return [
                'source' => 'intraday',
                'source_label' => 'Asignación intradiaria',
                'date' => $workDate->toDateString(),
                'employee_id' => $employeeId,
                'details' => [
                    'intraday_activity_id' => (int) $intradayAssignment->intradayActivity->id,
                    'activity_name' => $intradayAssignment->intradayActivity->name,
                    'start_time' => (string) $intradayAssignment->intradayActivity->start_time,
                    'end_time' => (string) $intradayAssignment->intradayActivity->end_time,
                    'weekly_schedule_id' => (int) $intradayAssignment->intradayActivity->weekly_schedule_id,
                ],
            ];
        }

        /** @var WeeklyScheduleAssignment|null $weeklyAssignment */
        $weeklyAssignment = WeeklyScheduleAssignment::query()
            ->with(['schedule', 'weeklySchedule'])
            ->where('employee_id', $employeeId)
            ->whereHas('weeklySchedule', function ($query) use ($workDate): void {
                $query->where('status', 'published')
                    ->whereDate('week_start_date', '<=', $workDate->toDateString())
                    ->whereDate('week_end_date', '>=', $workDate->toDateString());
            })
            ->orderByDesc('id')
            ->first();

        if ($weeklyAssignment !== null) {
            return [
                'source' => 'weekly',
                'source_label' => 'Asignación semanal publicada',
                'date' => $workDate->toDateString(),
                'employee_id' => $employeeId,
                'details' => [
                    'weekly_schedule_assignment_id' => (int) $weeklyAssignment->id,
                    'weekly_schedule_id' => (int) $weeklyAssignment->weekly_schedule_id,
                    'schedule_name' => $weeklyAssignment->schedule?->name,
                    'schedule_start_time' => (string) $weeklyAssignment->schedule?->start_time,
                    'schedule_end_time' => (string) $weeklyAssignment->schedule?->end_time,
                ],
            ];
        }

        return [
            'source' => 'none',
            'source_label' => 'Sin asignación vigente',
            'date' => $workDate->toDateString(),
            'employee_id' => $employeeId,
            'details' => [],
        ];
    }
}
