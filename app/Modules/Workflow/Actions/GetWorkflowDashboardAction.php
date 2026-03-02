<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Modules\Core\Models\IncidentType;
use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;
use App\Modules\Workflow\Models\LeaveRequest;

final readonly class GetWorkflowDashboardAction {
    public function __construct(
        private ResolveLeaveApproverAction $resolveLeaveApproverAction,
    ) {
    }

    /**
     * @return array{
     *   employee: Employee|null,
     *   myLeaveRequests:\Illuminate\Database\Eloquent\Collection<int, LeaveRequest>,
     *   pendingApprovals:\Illuminate\Database\Eloquent\Collection<int, LeaveRequest>,
     *   exceptionEmployees:\Illuminate\Database\Eloquent\Collection<int, Employee>,
     *   incidentTypes:\Illuminate\Database\Eloquent\Collection<int, IncidentType>,
     *   isCoordinator: bool,
     *   isWfm: bool
     * }
     */
    public function execute(int $userId): array {
        /** @var Employee|null $employee */
        $employee = Employee::query()->with('user')->where('user_id', $userId)->where('is_active', true)->first();

        if ($employee === null || $employee->user === null) {
            return [
                'employee' => null,
                'myLeaveRequests' => collect(),
                'pendingApprovals' => collect(),
                'exceptionEmployees' => collect(),
                'incidentTypes' => IncidentType::query()->orderBy('name')->get(),
                'isCoordinator' => false,
                'isWfm' => false,
            ];
        }

        $isWfm = $employee->user->hasAnyRole(['Administrador', 'Analista WFM']);
        $isCoordinator = $employee->user->hasRole('Coordinador');

        $myLeaveRequests = LeaveRequest::query()
            ->with(['incidentType', 'approvals.approver.user'])
            ->where('employee_id', $employee->id)
            ->orderByDesc('id')
            ->limit(40)
            ->get();

        $pendingCandidates = LeaveRequest::query()
            ->with(['employee.user', 'employee.teamMemberships.team.coordinator.user', 'incidentType'])
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

        $pendingApprovals = $pendingCandidates
            ->filter(function (LeaveRequest $request) use ($employee, $isWfm): bool {
                if ($isWfm) {
                    return true;
                }

                try {
                    $expected = $this->resolveLeaveApproverAction->execute($request);
                } catch (\Throwable) {
                    return false;
                }

                return (int) $expected->id === (int) $employee->id;
            })
            ->values();

        $exceptionEmployees = collect();

        if ($isWfm) {
            $exceptionEmployees = Employee::query()->where('is_active', true)->orderBy('first_name')->orderBy('last_name')->get();
        } elseif ($isCoordinator) {
            /** @var Team|null $team */
            $team = Team::query()->where('coordinator_employee_id', $employee->id)->where('is_active', true)->first();

            if ($team !== null) {
                $memberIds = $team->activeMembers()->pluck('employee_id')->all();

                $exceptionEmployees = Employee::query()
                    ->whereIn('id', $memberIds)
                    ->where('is_active', true)
                    ->orderBy('first_name')
                    ->orderBy('last_name')
                    ->get();
            }
        }

        return [
            'employee' => $employee,
            'myLeaveRequests' => $myLeaveRequests,
            'pendingApprovals' => $pendingApprovals,
            'exceptionEmployees' => $exceptionEmployees,
            'incidentTypes' => IncidentType::query()->orderBy('name')->get(),
            'isCoordinator' => $isCoordinator,
            'isWfm' => $isWfm,
        ];
    }
}
