<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Schedule\Models\WfmSetting;
use App\Modules\Team\Models\TeamMember;
use App\Modules\Workflow\Models\LeaveRequest;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

final readonly class ResolveLeaveApproverAction {
    /**
     * @throws ValidationException
     */
    public function execute(LeaveRequest $leaveRequest): Employee {
        /** @var Employee|null $requester */
        $requester = Employee::query()->with('user')->find($leaveRequest->employee_id);

        if ($requester === null || $requester->user === null) {
            throw ValidationException::withMessages([
                'leave_request' => 'No se pudo resolver el solicitante del permiso.',
            ]);
        }

        $requesterRoles = $requester->user->getRoleNames()->all();
        $requiresHigherApproval = $this->requiresHigherApproval($leaveRequest);

        if (in_array('Jefe', $requesterRoles, true)) {
            return $this->findAncestorByRole($requester, 'Director');
        }

        if (in_array('Coordinador', $requesterRoles, true)) {
            return $requiresHigherApproval
                ? $this->findAncestorByRole($requester, 'Director')
                : $this->findAncestorByRole($requester, 'Jefe');
        }

        if ($requiresHigherApproval) {
            return $this->findAncestorByRole($requester, 'Jefe');
        }

        return $this->findCoordinatorForEmployee($requester);
    }

    /**
     * @throws ValidationException
     */
    private function findCoordinatorForEmployee(Employee $employee): Employee {
        /** @var TeamMember|null $membership */
        $membership = TeamMember::query()
            ->with('team.coordinator.user')
            ->active()
            ->where('employee_id', $employee->id)
            ->orderByDesc('start_date')
            ->first();

        $coordinator = $membership?->team?->coordinator;

        if ($coordinator === null || $coordinator->user === null || !$coordinator->user->hasRole('Coordinador')) {
            throw ValidationException::withMessages([
                'leave_request' => 'No existe coordinador directo válido para el solicitante.',
            ]);
        }

        return $coordinator;
    }

    /**
     * @throws ValidationException
     */
    private function findAncestorByRole(Employee $employee, string $targetRole): Employee {
        $cursor = $employee;

        for ($depth = 0; $depth < 8; $depth++) {
            /** @var Employee|null $parent */
            $parent = Employee::query()->with('user')->find($cursor->parent_id);

            if ($parent === null || $parent->user === null) {
                break;
            }

            if ($parent->user->hasRole($targetRole)) {
                return $parent;
            }

            $cursor = $parent;
        }

        throw ValidationException::withMessages([
            'leave_request' => "No se pudo resolver un aprobador con rol {$targetRole} en la jerarquía.",
        ]);
    }

    private function requiresHigherApproval(LeaveRequest $leaveRequest): bool {
        /** @var WfmSetting|null $wfmSetting */
        $wfmSetting = WfmSetting::query()->orderByDesc('id')->first();

        $thresholdHours = $wfmSetting?->approval_threshold_hours ?? 8;
        $start = CarbonImmutable::parse((string) $leaveRequest->start_datetime);
        $end = CarbonImmutable::parse((string) $leaveRequest->end_datetime);
        $durationHours = max(1, $start->diffInHours($end));

        $typeName = mb_strtolower((string) $leaveRequest->incidentType?->name);
        $isVacation = str_contains($typeName, 'vacaci');
        $isIncapacity = str_contains($typeName, 'incapac');

        return $durationHours > $thresholdHours || $isVacation || $isIncapacity;
    }
}
