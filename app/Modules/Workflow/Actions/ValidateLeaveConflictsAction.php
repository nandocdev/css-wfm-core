<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Modules\Attendance\Models\AttendanceIncident;
use App\Modules\Workflow\Models\LeaveRequest;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

final readonly class ValidateLeaveConflictsAction {
    /**
     * @throws ValidationException
     */
    public function execute(int $employeeId, string $startDateTime, string $endDateTime, ?int $ignoreLeaveRequestId = null): void {
        $start = CarbonImmutable::parse($startDateTime);
        $end = CarbonImmutable::parse($endDateTime);

        $leaveConflictQuery = LeaveRequest::query()
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('start_datetime', '<', $end->toDateTimeString())
            ->where('end_datetime', '>', $start->toDateTimeString());

        if ($ignoreLeaveRequestId !== null) {
            $leaveConflictQuery->whereKeyNot($ignoreLeaveRequestId);
        }

        if ($leaveConflictQuery->exists()) {
            throw ValidationException::withMessages([
                'start_datetime' => 'Existe un permiso aprobado que se solapa con el rango solicitado.',
            ]);
        }

        $attendanceConflictExists = AttendanceIncident::query()
            ->where('employee_id', $employeeId)
            ->whereNotNull('justification')
            ->whereDate('incident_date', '>=', $start->toDateString())
            ->whereDate('incident_date', '<=', $end->toDateString())
            ->exists();

        if ($attendanceConflictExists) {
            throw ValidationException::withMessages([
                'start_datetime' => 'Existe una incidencia justificada que entra en conflicto con el rango solicitado.',
            ]);
        }
    }
}
