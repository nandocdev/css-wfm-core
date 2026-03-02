<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Actions;

use App\Contracts\Core\NotificationDispatcherContract;
use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;
use App\Modules\Team\Models\TeamMember;
use Illuminate\Validation\ValidationException;

final readonly class EscalateOperationalIncidentAction {
    public function __construct(
        private NotificationDispatcherContract $notificationDispatcher,
    ) {
    }

    /**
     * @param  array{employee_id:int, severity:string, details:string}  $payload
     * @throws ValidationException
     */
    public function execute(int $supervisorUserId, array $payload): void {
        /** @var Employee|null $supervisor */
        $supervisor = Employee::query()->where('user_id', $supervisorUserId)->where('is_active', true)->first();

        if ($supervisor === null) {
            throw ValidationException::withMessages([
                'employee_id' => 'No existe un empleado activo vinculado al usuario autenticado.',
            ]);
        }

        /** @var TeamMember|null $membership */
        $membership = TeamMember::query()->active()->where('employee_id', $supervisor->id)->first();

        if ($membership === null) {
            throw ValidationException::withMessages([
                'employee_id' => 'No estás asociado a un equipo activo para escalar incidencias.',
            ]);
        }

        /** @var Team $team */
        $team = Team::query()->with(['coordinator.user'])->whereKey($membership->team_id)->firstOrFail();

        $inTeam = $team->activeMembers()->where('employee_id', $payload['employee_id'])->exists();

        if (!$inTeam) {
            throw ValidationException::withMessages([
                'employee_id' => 'El colaborador seleccionado no pertenece a tu equipo.',
            ]);
        }

        $coordinatorUser = $team->coordinator?->user;

        if ($coordinatorUser === null) {
            throw ValidationException::withMessages([
                'employee_id' => 'Tu equipo no tiene coordinador activo para recibir la escalación.',
            ]);
        }

        /** @var Employee|null $targetEmployee */
        $targetEmployee = Employee::query()->find($payload['employee_id']);

        $this->notificationDispatcher->dispatch(
            [$coordinatorUser],
            'Escalación operativa en piso',
            sprintf(
                'Supervisor %s %s reporta incidencia para %s %s (%s).',
                $supervisor->first_name,
                $supervisor->last_name,
                $targetEmployee?->first_name ?? 'N/A',
                $targetEmployee?->last_name ?? 'N/A',
                strtoupper($payload['severity']),
            ),
            '/attendance/coordinator/incidents',
            [
                'team_id' => $team->id,
                'supervisor_employee_id' => $supervisor->id,
                'target_employee_id' => $payload['employee_id'],
                'severity' => $payload['severity'],
                'details' => $payload['details'],
            ],
            'Escalación operativa de supervisor',
        );
    }
}
