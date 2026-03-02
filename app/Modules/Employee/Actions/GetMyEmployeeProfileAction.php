<?php

declare(strict_types=1);

namespace App\Modules\Employee\Actions;

use App\Modules\Employee\Models\Employee;

final readonly class GetMyEmployeeProfileAction {
    public function execute(int $userId): ?Employee {
        /** @var Employee|null $employee */
        $employee = Employee::query()
            ->with([
                'position.department.directorate',
                'employmentStatus',
                'parent.position.department.directorate',
                'teamMemberships.team',
                'dependents',
                'disabilities.disabilityType',
                'diseases.diseaseType',
            ])
            ->where('user_id', $userId)
            ->first();

        return $employee;
    }
}
