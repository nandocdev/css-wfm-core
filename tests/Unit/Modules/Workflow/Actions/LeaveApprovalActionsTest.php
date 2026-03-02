<?php

declare(strict_types=1);

use App\Contracts\Core\NotificationDispatcherContract;
use App\Modules\Employee\Models\Employee;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use App\Modules\Team\Models\Team;
use App\Modules\Team\Models\TeamMember;
use App\Modules\Workflow\Actions\ApproveLeaveRequestAction;
use App\Modules\Workflow\Actions\RejectLeaveRequestAction;
use App\Modules\Workflow\Actions\ResolveLeaveApproverAction;
use App\Modules\Workflow\Actions\ValidateLeaveConflictsAction;
use App\Modules\Workflow\Models\LeaveRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('approve leave request changes status and creates approval', function (): void {
    $catalog = createCatalogs();
    $requester = createEmployeeWithRole('Operador', $catalog, 'req_apr');
    $coordinator = createEmployeeWithRole('Coordinador', $catalog, 'coor_apr');

    $approvalTeam = Team::query()->create([
        'name' => 'TEAM-APR',
        'description' => 'Equipo de aprobación',
        'is_active' => true,
        'coordinator_employee_id' => $coordinator->id,
    ]);

    TeamMember::query()->create([
        'team_id' => $approvalTeam->id,
        'employee_id' => $requester->id,
        'start_date' => now()->toDateString(),
        'is_active' => true,
    ]);

    $leaveRequest = LeaveRequest::query()->create([
        'employee_id' => $requester->id,
        'incident_type_id' => $catalog['incident_type_id'],
        'type' => 'full',
        'start_datetime' => now()->addDay()->setTime(8, 0)->toDateTimeString(),
        'end_datetime' => now()->addDay()->setTime(12, 0)->toDateTimeString(),
        'justification' => 'Consulta médica',
        'status' => 'pending',
    ]);

    $notifier = new class implements NotificationDispatcherContract {
        public int $dispatches = 0;

        public function dispatch(iterable $notifiables, string $title, string $message, ?string $url = null, array $context = [], ?string $mailSubject = null): void {
            $this->dispatches++;
        }
    };

    $action = new ApproveLeaveRequestAction(
        new ResolveLeaveApproverAction(),
        new ValidateLeaveConflictsAction(),
        $notifier,
    );

    $action->execute($leaveRequest, (int) $coordinator->user_id, 'Aprobado por coordinación');

    $leaveRequest->refresh();

    expect($leaveRequest->status)->toBe('approved');
    expect(DB::table('leave_request_approvals')->where([
        'leave_request_id' => $leaveRequest->id,
        'approver_id' => $coordinator->id,
        'action' => 'approved',
        'step' => 1,
    ])->exists())->toBeTrue();
    expect($notifier->dispatches)->toBe(1);
});

test('reject leave request changes status and creates approval', function (): void {
    $catalog = createCatalogs();
    $requester = createEmployeeWithRole('Operador', $catalog, 'req_rej');
    $coordinator = createEmployeeWithRole('Coordinador', $catalog, 'coor_rej');

    $rejectionTeam = Team::query()->create([
        'name' => 'TEAM-REJ',
        'description' => 'Equipo de rechazo',
        'is_active' => true,
        'coordinator_employee_id' => $coordinator->id,
    ]);

    TeamMember::query()->create([
        'team_id' => $rejectionTeam->id,
        'employee_id' => $requester->id,
        'start_date' => now()->toDateString(),
        'is_active' => true,
    ]);

    $leaveRequest = LeaveRequest::query()->create([
        'employee_id' => $requester->id,
        'incident_type_id' => $catalog['incident_type_id'],
        'type' => 'full',
        'start_datetime' => now()->addDay()->setTime(13, 0)->toDateTimeString(),
        'end_datetime' => now()->addDay()->setTime(17, 0)->toDateTimeString(),
        'justification' => 'Trámite',
        'status' => 'pending',
    ]);

    $notifier = new class implements NotificationDispatcherContract {
        public int $dispatches = 0;

        public function dispatch(iterable $notifiables, string $title, string $message, ?string $url = null, array $context = [], ?string $mailSubject = null): void {
            $this->dispatches++;
        }
    };

    $action = new RejectLeaveRequestAction(
        new ResolveLeaveApproverAction(),
        $notifier,
    );

    $action->execute($leaveRequest, (int) $coordinator->user_id, 'No procede');

    $leaveRequest->refresh();

    expect($leaveRequest->status)->toBe('rejected');
    expect(DB::table('leave_request_approvals')->where([
        'leave_request_id' => $leaveRequest->id,
        'approver_id' => $coordinator->id,
        'action' => 'rejected',
        'step' => 1,
    ])->exists())->toBeTrue();
    expect($notifier->dispatches)->toBe(1);
});

/**
 * @return array{township_id:int, department_id:int, position_id:int, employment_status_id:int, incident_type_id:int}
 */
function createCatalogs(): array {
    $directorateId = (int) DB::table('directorates')->insertGetId([
        'name' => 'DIR-TEST-' . random_int(10, 99),
        'description' => 'Dirección de pruebas',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $departmentId = (int) DB::table('departments')->insertGetId([
        'name' => 'DEP-TEST-' . random_int(10, 99),
        'description' => 'Departamento de pruebas',
        'directorate_id' => $directorateId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $positionId = (int) DB::table('positions')->insertGetId([
        'title' => 'Operador Test ' . random_int(10, 99),
        'description' => 'Posición test',
        'position_code' => 'POS-' . now()->format('His') . '-' . random_int(100, 999),
        'department_id' => $departmentId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $provinceId = (int) DB::table('provinces')->insertGetId([
        'name' => 'PROV-TEST-' . random_int(10, 99),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $districtId = (int) DB::table('districts')->insertGetId([
        'name' => 'DIST-TEST-' . random_int(10, 99),
        'province_id' => $provinceId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $townshipId = (int) DB::table('townships')->insertGetId([
        'name' => 'TOWN-TEST-' . random_int(10, 99),
        'district_id' => $districtId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $employmentStatusId = (int) DB::table('employment_statuses')->insertGetId([
        'name' => 'ACTIVO-TEST-' . random_int(10, 99),
        'description' => 'Estado activo',
        'code' => 'ACT-' . random_int(10, 99),
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $incidentTypeId = (int) DB::table('incident_types')->insertGetId([
        'code' => 'PERM-' . random_int(100, 999),
        'name' => 'Permiso Corto',
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
function createEmployeeWithRole(string $roleName, array $catalog, string $suffix): Employee {
    $user = User::query()->create([
        'name' => 'User ' . $suffix,
        'email' => $suffix . '.' . random_int(10, 99) . '@example.test',
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
        'employee_number' => 'EMP-' . $suffix . '-' . random_int(10, 99),
        'user_id' => $user->id,
        'username' => 'usr_' . $suffix . '_' . random_int(10, 99),
        'first_name' => 'Nombre' . $suffix,
        'last_name' => 'Apellido' . $suffix,
        'email' => $suffix . '.employee.' . random_int(10, 99) . '@example.test',
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
