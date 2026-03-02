<?php

declare(strict_types=1);

namespace App\Modules\Team\Actions;

use App\Modules\Team\Models\Team;

final readonly class GetCoordinatorTeamAction {
    public function execute(int $employeeId): ?Team {
        /** @var Team|null $team */
        $team = Team::query()
            ->with(['activeMembers.employee.position.department.directorate', 'activeMembers.employee.user', 'coordinator.user'])
            ->where('coordinator_employee_id', $employeeId)
            ->first();

        return $team;
    }
}
