<?php

declare(strict_types=1);

namespace App\Modules\Team\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;
use App\Modules\Team\Models\TeamMember;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class AddTeamMemberAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(array $payload): TeamMember {
        $teamId = (int) $payload['team_id'];
        $employeeId = (int) $payload['employee_id'];

        if (!Team::query()->whereKey($teamId)->exists()) {
            throw ValidationException::withMessages(['team_id' => 'El equipo seleccionado no existe.']);
        }

        if (!Employee::query()->whereKey($employeeId)->exists()) {
            throw ValidationException::withMessages(['employee_id' => 'El empleado seleccionado no existe.']);
        }

        $activeMembership = TeamMember::query()
            ->active()
            ->where('employee_id', $employeeId)
            ->first();

        if ($activeMembership !== null && (int) $activeMembership->team_id !== $teamId) {
            throw ValidationException::withMessages([
                'employee_id' => 'El empleado ya está asignado activamente a otro equipo.',
            ]);
        }

        /** @var TeamMember $member */
        $member = $this->databaseManager->transaction(function () use ($payload, $activeMembership): TeamMember {
            if ($activeMembership !== null) {
                return $activeMembership;
            }

            /** @var TeamMember $created */
            $created = TeamMember::query()->create([
                'team_id' => $payload['team_id'],
                'employee_id' => $payload['employee_id'],
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'] ?? null,
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]);

            return $created;
        });

        return $member;
    }
}
