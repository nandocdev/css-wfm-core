<?php

declare(strict_types=1);

namespace App\Modules\Organization\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Organization\Models\Directorate;

final readonly class GetOrganizationHierarchyAction {
    /**
     * @return array{directorates: \Illuminate\Database\Eloquent\Collection<int, Directorate>, hierarchyRoots: \Illuminate\Database\Eloquent\Collection<int, Employee>}
     */
    public function execute(): array {
        $directorates = Directorate::query()
            ->with(['departments.positions'])
            ->orderBy('name')
            ->get();

        $hierarchyRoots = Employee::query()
            ->with(['position.department.directorate', 'descendants'])
            ->whereNull('parent_id')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return [
            'directorates' => $directorates,
            'hierarchyRoots' => $hierarchyRoots,
        ];
    }
}
