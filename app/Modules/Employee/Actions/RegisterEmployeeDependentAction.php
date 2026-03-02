<?php

declare(strict_types=1);

namespace App\Modules\Employee\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Employee\Models\EmployeeDependent;
use Illuminate\Database\DatabaseManager;

final readonly class RegisterEmployeeDependentAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(Employee $employee, array $payload): EmployeeDependent {
        /** @var EmployeeDependent $dependent */
        $dependent = $this->databaseManager->transaction(function () use ($employee, $payload): EmployeeDependent {
            /** @var EmployeeDependent $created */
            $created = EmployeeDependent::query()->create([
                'employee_id' => $employee->id,
                'first_name' => $payload['first_name'],
                'last_name' => $payload['last_name'],
                'relationship' => $payload['relationship'],
                'birth_date' => $payload['birth_date'],
                'is_dependent' => (bool) ($payload['is_dependent'] ?? true),
            ]);

            return $created;
        });

        return $dependent;
    }
}
