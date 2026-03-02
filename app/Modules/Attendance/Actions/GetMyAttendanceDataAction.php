<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Actions;

use App\Modules\Attendance\Models\AttendanceIncident;
use App\Modules\Employee\Models\Employee;
use Carbon\CarbonImmutable;

final readonly class GetMyAttendanceDataAction {
    /**
     * @return array{
     *   employee: Employee|null,
     *   todayIncidents: \Illuminate\Database\Eloquent\Collection<int, AttendanceIncident>,
     *   historyIncidents: \Illuminate\Database\Eloquent\Collection<int, AttendanceIncident>
     * }
     */
    public function execute(int $userId, string $fromDate, string $toDate): array {
        /** @var Employee|null $employee */
        $employee = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($employee === null) {
            return [
                'employee' => null,
                'todayIncidents' => collect(),
                'historyIncidents' => collect(),
            ];
        }

        $today = CarbonImmutable::today()->toDateString();

        return [
            'employee' => $employee,
            'todayIncidents' => AttendanceIncident::query()
                ->with(['incidentType', 'recordedBy.user'])
                ->where('employee_id', $employee->id)
                ->whereDate('incident_date', $today)
                ->orderByDesc('id')
                ->get(),
            'historyIncidents' => AttendanceIncident::query()
                ->with(['incidentType', 'recordedBy.user'])
                ->where('employee_id', $employee->id)
                ->whereDate('incident_date', '>=', $fromDate)
                ->whereDate('incident_date', '<=', $toDate)
                ->orderByDesc('incident_date')
                ->orderByDesc('id')
                ->get(),
        ];
    }
}
