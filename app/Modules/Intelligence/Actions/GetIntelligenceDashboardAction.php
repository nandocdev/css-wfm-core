<?php

declare(strict_types=1);

namespace App\Modules\Intelligence\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Workflow\Models\LeaveRequest;

final readonly class GetIntelligenceDashboardAction {
    /**
     * @return array{
     *   employee: Employee|null,
     *   pendingInstitutionalExceptions:\Illuminate\Database\Eloquent\Collection<int, LeaveRequest>,
     *   activeEmployees:\Illuminate\Database\Eloquent\Collection<int, Employee>,
     *   canForceApprove: bool,
     *   canReprocess: bool
     * }
     */
    public function execute(int $userId): array {
        /** @var Employee|null $employee */
        $employee = Employee::query()->with('user')->where('user_id', $userId)->where('is_active', true)->first();

        if ($employee === null || $employee->user === null) {
            return [
                'employee' => null,
                'pendingInstitutionalExceptions' => collect(),
                'activeEmployees' => collect(),
                'canForceApprove' => false,
                'canReprocess' => false,
            ];
        }

        $canForceApprove = $employee->user->hasAnyRole(['Administrador', 'Analista WFM', 'Director', 'Jefe']);
        $canReprocess = $employee->user->hasRole('Administrador');

        $pendingInstitutionalExceptions = collect();

        if ($canForceApprove) {
            $pendingInstitutionalExceptions = LeaveRequest::query()
                ->with(['employee.user', 'incidentType'])
                ->where('status', 'pending')
                ->orderByDesc('id')
                ->limit(50)
                ->get();
        }

        $activeEmployees = collect();

        if ($canReprocess) {
            $activeEmployees = Employee::query()
                ->where('is_active', true)
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->limit(300)
                ->get();
        }

        return [
            'employee' => $employee,
            'pendingInstitutionalExceptions' => $pendingInstitutionalExceptions,
            'activeEmployees' => $activeEmployees,
            'canForceApprove' => $canForceApprove,
            'canReprocess' => $canReprocess,
        ];
    }
}
