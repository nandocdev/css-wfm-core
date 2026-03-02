<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Intelligence;

use App\Modules\Employee\Models\Employee;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use App\Modules\Workflow\Models\LeaveRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class ForceApproveInstitutionalExceptionFeatureTest extends TestCase {
    use RefreshDatabase;

    public function test_administrador_can_force_approve_institutional_exception(): void {
        $catalog = $this->createCatalogs();
        $actor = $this->createEmployeeWithRole('Administrador', $catalog, 'actor_force');
        $requester = $this->createEmployeeWithRole('Operador', $catalog, 'req_force');

        $leaveRequest = LeaveRequest::query()->create([
            'employee_id' => $requester->id,
            'incident_type_id' => $catalog['incident_type_id'],
            'type' => 'full',
            'start_datetime' => now()->addDay()->setTime(8, 0)->toDateTimeString(),
            'end_datetime' => now()->addDay()->setTime(16, 0)->toDateTimeString(),
            'justification' => 'Evento institucional',
            'status' => 'pending',
        ]);

        $this->actingAs($actor->user)
            ->post(route('intelligence.exceptions.force_approve', $leaveRequest), [
                'justification' => 'Aprobación excepcional por continuidad operativa',
            ])
            ->assertRedirect();

        $leaveRequest->refresh();

        $this->assertSame('approved', $leaveRequest->status);
        $this->assertDatabaseHas('leave_request_approvals', [
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => $actor->id,
            'action' => 'approved',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'LeaveRequest',
            'entity_id' => $leaveRequest->id,
            'action' => 'force_approved',
            'user_id' => $actor->user_id,
        ]);
    }

    /**
     * @return array{township_id:int, department_id:int, position_id:int, employment_status_id:int, incident_type_id:int}
     */
    private function createCatalogs(): array {
        $directorateId = (int) DB::table('directorates')->insertGetId([
            'name' => 'DIR-TEST-FEAT',
            'description' => 'Dirección de pruebas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $departmentId = (int) DB::table('departments')->insertGetId([
            'name' => 'DEP-TEST-FEAT',
            'description' => 'Departamento de pruebas',
            'directorate_id' => $directorateId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $positionId = (int) DB::table('positions')->insertGetId([
            'title' => 'Operador Test Feature',
            'description' => 'Posición test',
            'position_code' => 'POSF-' . now()->format('His') . '-' . random_int(100, 999),
            'department_id' => $departmentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $provinceId = (int) DB::table('provinces')->insertGetId([
            'name' => 'PROV-FEAT-' . random_int(10, 99),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $districtId = (int) DB::table('districts')->insertGetId([
            'name' => 'DIST-FEAT-' . random_int(10, 99),
            'province_id' => $provinceId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $townshipId = (int) DB::table('townships')->insertGetId([
            'name' => 'TOWN-FEAT-' . random_int(10, 99),
            'district_id' => $districtId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $employmentStatusId = (int) DB::table('employment_statuses')->insertGetId([
            'name' => 'ACTIVO-FEAT-' . random_int(10, 99),
            'description' => 'Estado activo',
            'code' => 'ACTF',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $incidentTypeId = (int) DB::table('incident_types')->insertGetId([
            'code' => 'PERF-' . random_int(100, 999),
            'name' => 'Permiso Institucional',
            'color' => 'blue',
            'requires_justification' => true,
            'affects_availability' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'township_id' => $townshipId,
            'department_id' => $departmentId,
            'position_id' => $positionId,
            'employment_status_id' => $employmentStatusId,
            'incident_type_id' => $incidentTypeId,
        ];
    }

    /**
     * @param array{township_id:int, department_id:int, position_id:int, employment_status_id:int, incident_type_id:int} $catalog
     */
    private function createEmployeeWithRole(string $roleName, array $catalog, string $suffix): Employee {
        $user = User::query()->create([
            'name' => 'User ' . $suffix,
            'email' => $suffix . '@example.test',
            'password' => 'password',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $role = Role::query()->firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);

        /** @var Employee $employee */
        $employee = Employee::query()->create([
            'employee_number' => 'EMP-' . $suffix,
            'user_id' => $user->id,
            'username' => 'usr_' . $suffix,
            'first_name' => 'Nombre' . $suffix,
            'last_name' => 'Apellido' . $suffix,
            'email' => $suffix . '.employee@example.test',
            'birth_date' => '1990-01-01',
            'township_id' => $catalog['township_id'],
            'department_id' => $catalog['department_id'],
            'position_id' => $catalog['position_id'],
            'employment_status_id' => $catalog['employment_status_id'],
            'hire_date' => '2024-01-01',
            'is_active' => true,
        ]);

        return $employee;
    }
}
