<?php

declare(strict_types=1);

namespace App\Modules\Employee\Actions;

use App\Modules\Core\Models\DisabilityType;
use App\Modules\Core\Models\DiseaseType;
use App\Modules\Core\Models\EmploymentStatus;
use App\Modules\Core\Models\Township;
use App\Modules\Employee\Models\Employee;
use App\Modules\Organization\Models\Department;
use App\Modules\Organization\Models\Position;
use App\Modules\Security\Models\User;
use App\Modules\Team\Models\Team;

final readonly class GetEmployeeManagementDataAction {
    /**
     * @return array{
     *     users: \Illuminate\Database\Eloquent\Collection<int, User>,
     *     employees: \Illuminate\Database\Eloquent\Collection<int, Employee>,
     *     positions: \Illuminate\Database\Eloquent\Collection<int, Position>,
     *     departments: \Illuminate\Database\Eloquent\Collection<int, Department>,
     *     statuses: \Illuminate\Database\Eloquent\Collection<int, EmploymentStatus>,
     *     townships: \Illuminate\Database\Eloquent\Collection<int, Township>,
     *     teams: \Illuminate\Database\Eloquent\Collection<int, Team>,
     *     disabilityTypes: \Illuminate\Database\Eloquent\Collection<int, DisabilityType>,
     *     diseaseTypes: \Illuminate\Database\Eloquent\Collection<int, DiseaseType>
     * }
     */
    public function execute(): array {
        $employeeUserIds = Employee::query()->pluck('user_id');

        return [
            'users' => User::query()
                ->whereNotIn('id', $employeeUserIds)
                ->whereDoesntHave('roles', static fn($query) => $query->where('name', 'Administrador'))
                ->orderBy('name')
                ->get(),
            'employees' => Employee::query()
                ->with(['position.department.directorate'])
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'positions' => Position::query()
                ->with('department.directorate')
                ->orderBy('title')
                ->get(),
            'departments' => Department::query()->orderBy('name')->get(),
            'statuses' => EmploymentStatus::query()->where('is_active', true)->orderBy('name')->get(),
            'townships' => Township::query()->orderBy('name')->get(),
            'teams' => Team::query()->where('is_active', true)->orderBy('name')->get(),
            'disabilityTypes' => DisabilityType::query()->orderBy('name')->get(),
            'diseaseTypes' => DiseaseType::query()->orderBy('name')->get(),
        ];
    }
}
