<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Attendance\Models\AttendanceIncident;
use App\Modules\Employee\Models\Employee;
use App\Modules\Workflow\Models\LeaveRequest;

final readonly class GetMyExceptionsAction {
    /**
     * @return array{leaveRequests:\Illuminate\Database\Eloquent\Collection<int, LeaveRequest>, attendanceIncidents:\Illuminate\Database\Eloquent\Collection<int, AttendanceIncident>}
     */
    public function execute(int $userId): array {
        /** @var Employee|null $employee */
        $employee = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($employee === null) {
            return [
                'leaveRequests' => LeaveRequest::query()->whereRaw('1 = 0')->get(),
                'attendanceIncidents' => AttendanceIncident::query()->whereRaw('1 = 0')->get(),
            ];
        }

        return [
            'leaveRequests' => LeaveRequest::query()
                ->with('incidentType')
                ->where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->orderByDesc('start_datetime')
                ->limit(100)
                ->get(),
            'attendanceIncidents' => AttendanceIncident::query()
                ->with('incidentType')
                ->where('employee_id', $employee->id)
                ->whereNotNull('justification')
                ->orderByDesc('incident_date')
                ->limit(100)
                ->get(),
        ];
    }
}
