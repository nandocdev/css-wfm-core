<?php

declare(strict_types=1);

namespace App\Modules\Employee\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\EmployeeDisability;
use Illuminate\Database\DatabaseManager;

final readonly class RegisterEmployeeDisabilityAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(Employee $employee, array $payload): EmployeeDisability {
        /** @var EmployeeDisability $disability */
        $disability = $this->databaseManager->transaction(function () use ($employee, $payload): EmployeeDisability {
            /** @var EmployeeDisability $created */
            $created = EmployeeDisability::query()->create([
                'employee_id' => $employee->id,
                'disability_type_id' => $payload['disability_type_id'],
                'description' => $payload['description'] ?? null,
                'diagnosis_date' => $payload['diagnosis_date'] ?? null,
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]);

            return $created;
        });

        return $disability;
    }
}
