<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Actions;

use App\Modules\Attendance\Models\AttendanceIncident;
use App\Modules\Core\Models\IncidentType;
use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;

final readonly class GetCoordinatorAttendanceDashboardAction {
    /**
     * @return array{
     *   team: Team|null,
     *   members: \Illuminate\Support\Collection<int, \App\Modules\Team\Models\TeamMember>,
     *   incidentTypes: \Illuminate\Database\Eloquent\Collection<int, IncidentType>,
     *   incidents: \Illuminate\Database\Eloquent\Collection<int, AttendanceIncident>
     * }
     */
    public function execute(int $userId): array {
        /** @var Employee|null $coordinator */
        $coordinator = Employee::query()->where('user_id', $userId)->where('is_active', true)->first();

        if ($coordinator === null) {
            return [
                'team' => null,
                'members' => collect(),
                'incidentTypes' => IncidentType::query()->orderBy('name')->get(),
                'incidents' => collect(),
            ];
        }

        /** @var Team|null $team */
        $team = Team::query()
            ->with(['activeMembers.employee.user', 'coordinator.user'])
            ->where('coordinator_employee_id', $coordinator->id)
            ->where('is_active', true)
            ->first();

        if ($team === null) {
            return [
                'team' => null,
                'members' => collect(),
                'incidentTypes' => IncidentType::query()->orderBy('name')->get(),
                'incidents' => collect(),
            ];
        }

        $memberIds = $team->activeMembers->pluck('employee_id')->map(static fn($id): int => (int) $id)->all();

        return [
            'team' => $team,
            'members' => $team->activeMembers,
            'incidentTypes' => IncidentType::query()->orderBy('name')->get(),
            'incidents' => AttendanceIncident::query()
                ->with(['employee.user', 'incidentType', 'recordedBy.user'])
                ->whereIn('employee_id', $memberIds)
                ->orderByDesc('incident_date')
                ->orderByDesc('id')
                ->limit(60)
                ->get(),
        ];
    }
}
