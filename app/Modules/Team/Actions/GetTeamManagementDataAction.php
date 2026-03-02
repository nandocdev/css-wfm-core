<?php

declare(strict_types=1);

namespace App\Modules\Team\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;

final readonly class GetTeamManagementDataAction {
    /**
     * @return array{
     *   teams:\Illuminate\Database\Eloquent\Collection<int, Team>,
     *   employees:\Illuminate\Database\Eloquent\Collection<int, Employee>,
     *   coordinators:\Illuminate\Database\Eloquent\Collection<int, Employee>
     * }
     */
    public function execute(): array {
        $teams = Team::query()
            ->with(['coordinator.user', 'activeMembers.employee.user'])
            ->orderBy('name')
            ->get();

        $employees = Employee::query()
            ->with('user')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $coordinators = Employee::query()
            ->with('user.roles')
            ->where('is_active', true)
            ->whereHas('user.roles', static fn($query) => $query->where('name', 'Coordinador'))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return [
            'teams' => $teams,
            'employees' => $employees,
            'coordinators' => $coordinators,
        ];
    }
}
