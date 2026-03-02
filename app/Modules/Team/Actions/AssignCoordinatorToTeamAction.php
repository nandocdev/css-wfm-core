<?php

declare(strict_types=1);

namespace App\Modules\Team\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;
use App\Modules\Team\Models\TeamMember;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class AssignCoordinatorToTeamAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    public function execute(Team $team, int $coordinatorEmployeeId): Team {
        /** @var Employee|null $coordinator */
        $coordinator = Employee::query()
            ->with('user.roles')
            ->find($coordinatorEmployeeId);

        if ($coordinator === null) {
            throw ValidationException::withMessages([
                'coordinator_employee_id' => 'El coordinador seleccionado no existe.',
            ]);
        }

        if (!$coordinator->user?->hasRole('Coordinador')) {
            throw ValidationException::withMessages([
                'coordinator_employee_id' => 'El empleado seleccionado no tiene rol de Coordinador.',
            ]);
        }

        $existingTeam = Team::query()
            ->where('coordinator_employee_id', $coordinatorEmployeeId)
            ->whereKeyNot($team->id)
            ->first();

        if ($existingTeam !== null) {
            throw ValidationException::withMessages([
                'coordinator_employee_id' => 'El coordinador ya está asignado a otro equipo.',
            ]);
        }

        /** @var Team $updated */
        $updated = $this->databaseManager->transaction(function () use ($team, $coordinatorEmployeeId): Team {
            $team->forceFill([
                'coordinator_employee_id' => $coordinatorEmployeeId,
            ])->save();

            $membership = TeamMember::query()
                ->active()
                ->where('team_id', $team->id)
                ->where('employee_id', $coordinatorEmployeeId)
                ->first();

            if ($membership === null) {
                TeamMember::query()->create([
                    'team_id' => $team->id,
                    'employee_id' => $coordinatorEmployeeId,
                    'start_date' => now()->toDateString(),
                    'is_active' => true,
                ]);
            }

            return $team->fresh(['coordinator.user']) ?? $team;
        });

        return $updated;
    }
}
