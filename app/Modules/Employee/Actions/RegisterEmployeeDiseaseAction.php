<?php

declare(strict_types=1);

namespace App\Modules\Employee\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\EmployeeDisease;
use Illuminate\Database\DatabaseManager;

final readonly class RegisterEmployeeDiseaseAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(Employee $employee, array $payload): EmployeeDisease {
        /** @var EmployeeDisease $disease */
        $disease = $this->databaseManager->transaction(function () use ($employee, $payload): EmployeeDisease {
            /** @var EmployeeDisease $created */
            $created = EmployeeDisease::query()->create([
                'employee_id' => $employee->id,
                'disease_type_id' => $payload['disease_type_id'],
                'description' => $payload['description'] ?? null,
                'diagnosis_date' => $payload['diagnosis_date'],
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]);

            return $created;
        });

        return $disease;
    }
}
