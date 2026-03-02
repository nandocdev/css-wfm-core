<?php

declare(strict_types=1);

namespace App\Modules\Employee\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\TeamMember;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class CreateEmployeeAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(array $payload): Employee {
        if (Employee::query()->where('user_id', $payload['user_id'])->exists()) {
            throw ValidationException::withMessages([
                'user_id' => 'El usuario ya tiene una ficha de empleado registrada.',
            ]);
        }

        /** @var Employee $employee */
        $employee = $this->databaseManager->transaction(function () use ($payload): Employee {
            /** @var Employee $created */
            $created = Employee::query()->create([
                'employee_number' => $payload['employee_number'] ?? null,
                'user_id' => $payload['user_id'],
                'username' => $payload['username'],
                'first_name' => $payload['first_name'],
                'last_name' => $payload['last_name'],
                'email' => $payload['email'],
                'birth_date' => $payload['birth_date'],
                'gender' => $payload['gender'] ?? null,
                'blood_type' => $payload['blood_type'] ?? null,
                'phone' => $payload['phone'] ?? null,
                'mobile_phone' => $payload['mobile_phone'] ?? null,
                'address' => $payload['address'] ?? null,
                'township_id' => $payload['township_id'],
                'department_id' => $payload['department_id'] ?? null,
                'parent_id' => $payload['parent_id'] ?? null,
                'position_id' => $payload['position_id'],
                'employment_status_id' => $payload['employment_status_id'],
                'hire_date' => $payload['hire_date'],
                'salary' => $payload['salary'] ?? null,
                'is_active' => (bool) ($payload['is_active'] ?? true),
                'is_manager' => (bool) ($payload['is_manager'] ?? false),
                'metadata' => $payload['metadata'] ?? null,
            ]);

            if (isset($payload['team_id']) && is_numeric($payload['team_id'])) {
                TeamMember::query()->create([
                    'team_id' => (int) $payload['team_id'],
                    'employee_id' => $created->id,
                    'start_date' => $payload['hire_date'],
                    'is_active' => true,
                ]);
            }

            return $created;
        });

        return $employee;
    }
}
