<?php

namespace Database\Seeders;

use App\Modules\Security\Models\Permission;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class CsvTableSeeder extends Seeder {
    /**
     * @var array<string, array<int, string>>
     */
    private const ROLE_PERMISSION_PATTERNS = [
        'Operador' => [
            'auth.',
            'security.profile.',
            'core.notifications.',
            'dashboard.view',
            'employee.profile.view_own',
            'employee.welfare.view_own',
            'planning.schedule.current.view_own',
            'planning.schedule.history.view_own',
            'planning.schedule.exceptions.view_own',
            'planning.my_day.view_own',
            'workflow.leave.request.',
            'workflow.shift_swap.request.',
            'attendance.self.',
        ],
        'Supervisor' => [
            'auth.',
            'security.profile.',
            'core.notifications.',
            'dashboard.view',
            'employee.profile.view_own',
            'employee.welfare.view_own',
            'planning.schedule.current.view_own',
            'planning.schedule.history.view_own',
            'planning.schedule.exceptions.view_own',
            'planning.my_day.view_own',
            'workflow.leave.request.',
            'workflow.shift_swap.request.',
            'attendance.self.',
            'team.schedule.published.view',
            'team.intraday.availability.view',
            'attendance.escalation.',
        ],
        'Coordinador' => [
            'auth.',
            'security.profile.',
            'core.notifications.',
            'dashboard.view',
            'team.members.view_direct',
            'attendance.incidents.team.',
            'workflow.leave.approval.review_team',
            'workflow.shift_swap.approval.review_team',
            'workflow.inbox.team.view',
            'workflow.exceptions.direct.create',
            'planning.break_overrides.',
            'planning.team.published.view',
            'reports.team.compliance.view',
        ],
        'Jefe' => [
            'auth.',
            'security.profile.',
            'core.notifications.',
            'dashboard.view',
            'organization.hierarchy.full.view',
            'workflow.leave.approval.review_management',
            'workflow.exceptions.special.authorize',
            'reports.management.consolidated.view',
        ],
        'Analista WFM' => [
            'auth.',
            'security.profile.',
            'core.notifications.',
            'dashboard.view',
            'schedule.catalog.',
            'wfm.settings.update',
            'planning.weekly.',
            'planning.intraday.',
            'schedule.break_templates.assign',
            'workflow.exceptions.bulk.create',
            'reports.global.view',
            'reports.export.',
        ],
        'Director' => [
            'auth.',
            'security.profile.',
            'core.notifications.',
            'dashboard.view',
            'organization.hierarchy.full.view',
            'operations.global.view',
            'kpis.global.view',
            'workflow.leave.approval.review_direction',
            'workflow.exceptions.institutional.authorize',
            'reports.management.consolidated.view',
            'reports.global.view',
        ],
        'Administrador' => [
            '*',
        ],
    ];

    /**
     * Seed a table from a CSV file.
     *
     * @param string $table The table name
     * @param string $filename The CSV filename in database/data/
     * @param string $delimiter CSV delimiter
     */
    public function seedFromCsv(string $table, string $filename, string $delimiter = ',') {
        $path = database_path('data/' . $filename);

        if (!File::exists($path)) {
            $this->command->warn("File not found: $path");
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file, 0, $delimiter);

        if (!$header) {
            $this->command->error("Empty or invalid CSV: $filename");
            fclose($file);
            return;
        }

        $count = 0;
        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row, fn($value) => $value !== null && $value !== ''))) {
                continue;
            }

            $data = array_combine($header, $row);

            // Clean up values
            foreach ($data as $key => $value) {
                if ($value === '1' || strtolower($value) === 'true')
                    $data[$key] = true;
                elseif ($value === '0' || strtolower($value) === 'false')
                    $data[$key] = false;
                elseif ($value === '')
                    $data[$key] = null;
            }

            // Determine if timestamps are needed
            $insertData = $data;
            if (isset($insertData['password']) && !empty($insertData['password'])) {
                $insertData['password'] = bcrypt($insertData['password']);
            }
            if (!isset($data['created_at'])) {
                $insertData['created_at'] = now();
            }
            if (!isset($data['updated_at'])) {
                $insertData['updated_at'] = now();
            }

            DB::table($table)->insert($insertData);
            $count++;
        }

        fclose($file);
        $this->command->info("Seeded $count records into $table from $filename");
    }

    public function run(): void {
        Schema::disableForeignKeyConstraints();

        // Seeding order matters due to foreign key constraints
        $this->seedFromCsv('directorates', 'directorates.csv');
        $this->seedFromCsv('disability_types', 'disability_types.csv');
        $this->seedFromCsv('disease_types', 'disease_types.csv');
        $this->seedFromCsv('employment_statuses', 'employment_statuses.csv');
        $this->seedFromCsv('provinces', 'provinces.csv');
        $this->seedFromCsv('teams', 'teams.csv');
        $this->seedFromCsv('departments', 'departments.csv'); // Depends on directorates
        $this->seedFromCsv('districts', 'districts.csv'); // Depends on provinces
        $this->seedFromCsv('townships', 'townships.csv'); // Depends on districts
        $this->seedFromCsv('positions', 'positions.csv'); // Depends on departments
        $this->seedFromCsv('incident_types', 'incident_types.csv');
        $this->seedFromCsv('schedules', 'schedules.csv');

        // Identity & Access
        $this->seedFromCsv('permissions', 'permissions.csv');
        $this->seedFromCsv('roles', 'roles.csv');
        $this->syncRolePermissions();
        $this->seedFromCsv('users', 'users.csv');
        $this->syncUserRolesFromCsv('user_roles.csv');
        $this->seedFromCsv('employees', 'employees.csv');

        Schema::enableForeignKeyConstraints();
    }

    private function syncRolePermissions(): void {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /** @var \Illuminate\Database\Eloquent\Collection<int, Permission> $allPermissions */
        $allPermissions = Permission::query()->where('guard_name', 'web')->get();

        foreach (self::ROLE_PERMISSION_PATTERNS as $roleName => $patterns) {
            /** @var Role|null $role */
            $role = Role::query()->where('name', $roleName)->first();

            if ($role === null) {
                $this->command->warn("Rol no encontrado para sincronizar permisos: {$roleName}");
                continue;
            }

            if (in_array('*', $patterns, true)) {
                $role->syncPermissions($allPermissions);
                continue;
            }

            $permissions = $allPermissions
                ->filter(function (Permission $permission) use ($patterns): bool {
                    foreach ($patterns as $pattern) {
                        if ($permission->name === $pattern || str_starts_with($permission->name, $pattern)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->values();

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->command->info('Permisos sincronizados por rol correctamente.');
    }

    private function syncUserRolesFromCsv(string $filename, string $delimiter = ','): void {
        $path = database_path('data/' . $filename);

        if (!File::exists($path)) {
            $this->command->warn("No existe {$filename}; se omite asignación masiva usuario-rol.");
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file, 0, $delimiter);

        if (!$header) {
            $this->command->error("Empty or invalid CSV: $filename");
            fclose($file);
            return;
        }

        $synced = 0;

        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            if (empty(array_filter($row, fn($value) => $value !== null && $value !== ''))) {
                continue;
            }

            $data = array_combine($header, $row);

            if (!is_array($data)) {
                continue;
            }

            $email = (string) ($data['user_email'] ?? '');
            $roleName = (string) ($data['role_name'] ?? '');

            if ($email === '' || $roleName === '') {
                continue;
            }

            /** @var User|null $user */
            $user = User::query()->where('email', $email)->first();

            /** @var Role|null $role */
            $role = Role::query()->where('name', $roleName)->first();

            if ($user === null || $role === null) {
                $this->command->warn("No se pudo asignar rol {$roleName} al usuario {$email}.");
                continue;
            }

            $user->syncRoles([$role]);
            $synced++;
        }

        fclose($file);

        $this->command->info("Asignaciones usuario-rol sincronizadas: {$synced}");
    }
}
