<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\EmployeeBreakOverride;
use App\Modules\Team\Models\Team;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetCoordinatorBreakManagementDataAction {
    /**
     * @return array{
     *   team: Team|null,
     *   members: Collection<int, \App\Modules\Team\Models\TeamMember>,
     *   overrides: Collection<int, EmployeeBreakOverride>
     * }
     */
    public function execute(int $userId): array {
        /** @var Employee|null $coordinator */
        $coordinator = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($coordinator === null) {
            return ['team' => null, 'members' => collect(), 'overrides' => collect()];
        }

        /** @var Team|null $team */
        $team = Team::query()
            ->with(['activeMembers.employee.user'])
            ->where('coordinator_employee_id', $coordinator->id)
            ->where('is_active', true)
            ->first();

        if ($team === null) {
            return ['team' => null, 'members' => collect(), 'overrides' => collect()];
        }

        $memberIds = $team->activeMembers->pluck('employee_id')->map(static fn($id): int => (int) $id)->all();

        /** @var Collection<int, EmployeeBreakOverride> $overrides */
        $overrides = EmployeeBreakOverride::query()
            ->with(['employee.user', 'createdBy'])
            ->whereIn('employee_id', $memberIds)
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return [
            'team' => $team,
            'members' => $team->activeMembers,
            'overrides' => $overrides,
        ];
    }
}
