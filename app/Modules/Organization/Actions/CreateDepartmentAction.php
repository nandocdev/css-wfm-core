<?php

declare(strict_types=1);

namespace App\Modules\Organization\Actions;

use App\Modules\Organization\Models\Department;
use Illuminate\Database\DatabaseManager;

final readonly class CreateDepartmentAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array{name:string,directorate_id:int,description?:string|null} $payload
     */
    public function execute(array $payload): Department {
        /** @var Department $department */
        $department = $this->databaseManager->transaction(function () use ($payload): Department {
            return Department::query()->create([
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
                'directorate_id' => $payload['directorate_id'],
            ]);
        });

        return $department;
    }
}
