<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Actions;

use App\Modules\Attendance\Models\AttendanceIncident;
use App\Modules\Employee\Models\Employee;
use App\Modules\Organization\Models\Department;
use App\Modules\Organization\Models\Directorate;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Team\Models\Team;
use App\Modules\Workflow\Models\LeaveRequest;
use Carbon\CarbonImmutable;

final readonly class BuildAnalyticsMonitoringDataAction {
    /**
     * @param array{start_date?:string, end_date?:string} $filters
     * @return array{
     *   canExecutive: bool,
     *   canManagement: bool,
     *   canCoordinator: bool,
     *   canReadonly: bool,
     *   canExport: bool,
     *   startDate: string,
     *   endDate: string,
     *   globalKpis: array<string, int|float|string>,
     *   executiveRows: array<int, array<string, int|float|string>>,
     *   managementRows: array<int, array<string, int|float|string>>,
     *   coordinatorRows: array<int, array<string, int|float|string>>,
     *   readonlyMonitoring: array{incidents: array<int, array<string, string>>, pending_leaves: array<int, array<string, string>>, team_snapshot: array<int, array<string, int|string>>}
     * }
     */
    public function execute(int $userId, array $filters = []): array {
        /** @var Employee|null $employee */
        $employee = Employee::query()->with('user')->where('user_id', $userId)->where('is_active', true)->first();
        abort_if($employee === null || $employee->user === null, 403);

        $startDate = isset($filters['start_date'])
            ? CarbonImmutable::parse((string) $filters['start_date'])->startOfDay()
            : CarbonImmutable::now()->subDays(30)->startOfDay();

        $endDate = isset($filters['end_date'])
            ? CarbonImmutable::parse((string) $filters['end_date'])->endOfDay()
            : CarbonImmutable::now()->endOfDay();

        $canExecutive = $employee->user->hasAnyRole(['Administrador', 'Director', 'Jefe']);
        $canManagement = $employee->user->hasAnyRole(['Administrador', 'Analista WFM', 'Director', 'Jefe']);
        $canCoordinator = $employee->user->hasAnyRole(['Administrador', 'Director', 'Jefe', 'Coordinador']);
        $canReadonly = $employee->user->hasAnyRole(['Administrador', 'Supervisor', 'Coordinador']);
        $canExport = $employee->user->hasAnyRole(['Administrador', 'Analista WFM', 'Director', 'Jefe']);

        $activeEmployeesCount = Employee::query()->where('is_active', true)->count();

        $affectedByIncidents = AttendanceIncident::query()
            ->whereBetween('incident_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('incidentType', fn($query) => $query->where('affects_availability', true))
            ->pluck('employee_id');

        $affectedByLeaves = LeaveRequest::query()
            ->where('status', 'approved')
            ->where('start_datetime', '<=', $endDate)
            ->where('end_datetime', '>=', $startDate)
            ->pluck('employee_id');

        $absentEmployeesCount = $affectedByIncidents
            ->merge($affectedByLeaves)
            ->unique()
            ->count();

        $today = CarbonImmutable::now()->toDateString();

        $coveredEmployeesCount = WeeklyScheduleAssignment::query()
            ->whereHas('employee', fn($query) => $query->where('is_active', true))
            ->whereHas('weeklySchedule', function ($query) use ($today): void {
                $query->where('status', 'published')
                    ->whereDate('week_start_date', '<=', $today)
                    ->whereDate('week_end_date', '>=', $today);
            })
            ->distinct('employee_id')
            ->count('employee_id');

        $absenteeismRate = $activeEmployeesCount > 0
            ? round(($absentEmployeesCount / $activeEmployeesCount) * 100, 2)
            : 0.0;

        $coverageRate = $activeEmployeesCount > 0
            ? round(($coveredEmployeesCount / $activeEmployeesCount) * 100, 2)
            : 0.0;

        return [
            'canExecutive' => $canExecutive,
            'canManagement' => $canManagement,
            'canCoordinator' => $canCoordinator,
            'canReadonly' => $canReadonly,
            'canExport' => $canExport,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'globalKpis' => [
                'active_employees' => $activeEmployeesCount,
                'absent_employees' => $absentEmployeesCount,
                'covered_employees' => $coveredEmployeesCount,
                'absenteeism_rate' => $absenteeismRate,
                'coverage_rate' => $coverageRate,
            ],
            'executiveRows' => $this->buildExecutiveRows($startDate, $endDate),
            'managementRows' => $this->buildManagementRows($startDate, $endDate),
            'coordinatorRows' => $this->buildCoordinatorRows($startDate, $endDate),
            'readonlyMonitoring' => $this->buildReadonlyMonitoring($startDate, $endDate),
        ];
    }

    /**
     * @return array<int, array<string, int|float|string>>
     */
    private function buildExecutiveRows(CarbonImmutable $startDate, CarbonImmutable $endDate): array {
        $rows = [];

        $directorates = Directorate::query()->with('departments.employees')->orderBy('name')->get();

        foreach ($directorates as $directorate) {
            $employeeIds = $directorate->departments
                ->flatMap(fn($department) => $department->employees->where('is_active', true)->pluck('id'))
                ->unique()
                ->values()
                ->all();

            $headcount = count($employeeIds);
            if ($headcount === 0) {
                continue;
            }

            $incidents = AttendanceIncident::query()
                ->whereIn('employee_id', $employeeIds)
                ->whereBetween('incident_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->whereHas('incidentType', fn($query) => $query->where('affects_availability', true))
                ->distinct('employee_id')
                ->count('employee_id');

            $covered = WeeklyScheduleAssignment::query()
                ->whereIn('employee_id', $employeeIds)
                ->distinct('employee_id')
                ->count('employee_id');

            $rows[] = [
                'directorate' => (string) $directorate->name,
                'headcount' => $headcount,
                'absent_employees' => $incidents,
                'coverage_percent' => round(($covered / $headcount) * 100, 2),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, int|float|string>>
     */
    private function buildManagementRows(CarbonImmutable $startDate, CarbonImmutable $endDate): array {
        $rows = [];

        $departments = Department::query()->with('directorate', 'employees')->orderBy('name')->get();

        foreach ($departments as $department) {
            $employeeIds = $department->employees
                ->where('is_active', true)
                ->pluck('id')
                ->unique()
                ->values()
                ->all();

            $headcount = count($employeeIds);
            if ($headcount === 0) {
                continue;
            }

            $approvedLeaves = LeaveRequest::query()
                ->whereIn('employee_id', $employeeIds)
                ->where('status', 'approved')
                ->where('start_datetime', '<=', $endDate)
                ->where('end_datetime', '>=', $startDate)
                ->count();

            $incidents = AttendanceIncident::query()
                ->whereIn('employee_id', $employeeIds)
                ->whereBetween('incident_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->count();

            $rows[] = [
                'directorate' => (string) ($department->directorate?->name ?? 'N/D'),
                'department' => (string) $department->name,
                'headcount' => $headcount,
                'approved_leaves' => $approvedLeaves,
                'incidents' => $incidents,
                'incidents_per_100' => round(($incidents / $headcount) * 100, 2),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, int|float|string>>
     */
    private function buildCoordinatorRows(CarbonImmutable $startDate, CarbonImmutable $endDate): array {
        $rows = [];

        $teams = Team::query()
            ->with(['coordinator.user', 'activeMembers.employee'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        foreach ($teams as $team) {
            $employeeIds = $team->activeMembers
                ->pluck('employee_id')
                ->unique()
                ->values()
                ->all();

            $headcount = count($employeeIds);
            if ($headcount === 0) {
                continue;
            }

            $availabilityImpacts = AttendanceIncident::query()
                ->whereIn('employee_id', $employeeIds)
                ->whereBetween('incident_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->whereHas('incidentType', fn($query) => $query->where('affects_availability', true))
                ->distinct('employee_id')
                ->count('employee_id');

            $complianceRate = round(max(0, 100 - (($availabilityImpacts / $headcount) * 100)), 2);

            $rows[] = [
                'team' => (string) $team->name,
                'coordinator' => trim((string) ($team->coordinator?->first_name . ' ' . $team->coordinator?->last_name)),
                'headcount' => $headcount,
                'affected_members' => $availabilityImpacts,
                'compliance_percent' => $complianceRate,
            ];
        }

        return $rows;
    }

    /**
     * @return array{incidents: array<int, array<string, string>>, pending_leaves: array<int, array<string, string>>, team_snapshot: array<int, array<string, int|string>>}
     */
    private function buildReadonlyMonitoring(CarbonImmutable $startDate, CarbonImmutable $endDate): array {
        $incidents = AttendanceIncident::query()
            ->with(['employee', 'incidentType'])
            ->whereBetween('incident_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderByDesc('incident_date')
            ->limit(40)
            ->get()
            ->map(fn(AttendanceIncident $incident): array => [
                'date' => (string) $incident->incident_date,
                'employee' => trim((string) ($incident->employee?->first_name . ' ' . $incident->employee?->last_name)),
                'type' => (string) ($incident->incidentType?->name ?? 'N/D'),
            ])
            ->all();

        $pendingLeaves = LeaveRequest::query()
            ->with(['employee', 'incidentType'])
            ->where('status', 'pending')
            ->orderByDesc('start_datetime')
            ->limit(40)
            ->get()
            ->map(fn(LeaveRequest $leave): array => [
                'start' => (string) $leave->start_datetime,
                'end' => (string) $leave->end_datetime,
                'employee' => trim((string) ($leave->employee?->first_name . ' ' . $leave->employee?->last_name)),
                'type' => (string) ($leave->incidentType?->name ?? 'N/D'),
            ])
            ->all();

        $teamSnapshot = Team::query()
            ->withCount('activeMembers')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn(Team $team): array => [
                'team' => (string) $team->name,
                'active_members' => (int) $team->active_members_count,
            ])
            ->all();

        return [
            'incidents' => $incidents,
            'pending_leaves' => $pendingLeaves,
            'team_snapshot' => $teamSnapshot,
        ];
    }
}
