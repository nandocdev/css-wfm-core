<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;
use App\Modules\Team\Models\TeamMember;

final readonly class GetSupervisorEscalationDataAction {
    /**
     * @return array{
     *   team: Team|null,
     *   supervisor: Employee|null,
     *   members: \Illuminate\Support\Collection<int, TeamMember>,
     *   coordinatorUser: User|null
     * }
     */
    public function execute(int $userId): array {
        /** @var Employee|null $supervisor */
        $supervisor = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($supervisor === null) {
            return [
                'team' => null,
                'supervisor' => null,
                'members' => collect(),
                'coordinatorUser' => null,
            ];
        }

        /** @var TeamMember|null $membership */
        $membership = TeamMember::query()
            ->with(['team.coordinator.user'])
            ->active()
            ->where('employee_id', $supervisor->id)
            ->orderByDesc('start_date')
            ->first();

        if ($membership === null) {
            return [
                'team' => null,
                'supervisor' => $supervisor,
                'members' => collect(),
                'coordinatorUser' => null,
            ];
        }

        /** @var Team $team */
        $team = Team::query()
            ->with(['activeMembers.employee.user', 'coordinator.user'])
            ->whereKey($membership->team_id)
            ->where('is_active', true)
            ->firstOrFail();

        $members = $team->activeMembers->filter(
            static fn(TeamMember $member): bool => (int) $member->employee_id !== (int) $supervisor->id,
        )->values();

        return [
            'team' => $team,
            'supervisor' => $supervisor,
            'members' => $members,
            'coordinatorUser' => $team->coordinator?->user,
        ];
    }
}
