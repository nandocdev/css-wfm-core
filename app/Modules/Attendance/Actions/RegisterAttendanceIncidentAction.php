<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Actions;

use App\Modules\Attendance\Models\AttendanceIncident;
use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class RegisterAttendanceIncidentAction {
    /**
     * @param  array{employee_id:int, incident_type_id:int, incident_date:string, start_time:string|null, end_time:string|null, justification:string|null}  $payload
     * @throws ValidationException
     */
    public function execute(array $payload, int $coordinatorUserId): AttendanceIncident {
        /** @var Employee|null $coordinator */
        $coordinator = Employee::query()->where('user_id', $coordinatorUserId)->where('is_active', true)->first();

        if ($coordinator === null) {
            throw ValidationException::withMessages([
                'employee_id' => 'No existe un empleado activo vinculado al usuario autenticado.',
            ]);
        }

        /** @var Team|null $team */
        $team = Team::query()->where('coordinator_employee_id', $coordinator->id)->where('is_active', true)->first();

        if ($team === null || !$team->activeMembers()->where('employee_id', $payload['employee_id'])->exists()) {
            throw ValidationException::withMessages([
                'employee_id' => 'El colaborador seleccionado no pertenece al equipo del coordinador.',
            ]);
        }

        /** @var AttendanceIncident $incident */
        $incident = DB::transaction(function () use ($payload, $coordinator): AttendanceIncident {
            return AttendanceIncident::query()->create([
                'employee_id' => $payload['employee_id'],
                'incident_type_id' => $payload['incident_type_id'],
                'incident_date' => $payload['incident_date'],
                'start_time' => $payload['start_time'],
                'end_time' => $payload['end_time'],
                'justification' => $payload['justification'],
                'recorded_by' => $coordinator->id,
            ]);
        });

        return $incident;
    }
}
