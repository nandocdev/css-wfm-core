<?php

declare(strict_types=1);

namespace App\Modules\Employee\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Employee\Actions\CreateEmployeeAction;
use App\Modules\Employee\Actions\GetEmployeeManagementDataAction;
use App\Modules\Employee\Actions\ImportEmployeesAction;
use App\Modules\Employee\Actions\RegisterEmployeeDependentAction;
use App\Modules\Employee\Actions\RegisterEmployeeDisabilityAction;
use App\Modules\Employee\Actions\RegisterEmployeeDiseaseAction;
use App\Modules\Employee\Http\Requests\ImportEmployeesRequest;
use App\Modules\Employee\Http\Requests\StoreEmployeeDependentRequest;
use App\Modules\Employee\Http\Requests\StoreEmployeeDisabilityRequest;
use App\Modules\Employee\Http\Requests\StoreEmployeeDiseaseRequest;
use App\Modules\Employee\Http\Requests\StoreEmployeeRequest;
use App\Modules\Employee\Models\Employee;
use App\Modules\Security\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class EmployeeManagementController extends Controller {
    public function __construct(
        private GetEmployeeManagementDataAction $getEmployeeManagementDataAction,
        private CreateEmployeeAction $createEmployeeAction,
        private RegisterEmployeeDependentAction $registerEmployeeDependentAction,
        private RegisterEmployeeDisabilityAction $registerEmployeeDisabilityAction,
        private RegisterEmployeeDiseaseAction $registerEmployeeDiseaseAction,
        private ImportEmployeesAction $importEmployeesAction,
    ) {
    }

    public function manage(Request $request): View {
        $this->ensureAdministrator($request);
        $data = $this->getEmployeeManagementDataAction->execute();

        return view('employee::admin.manage', $data);
    }

    public function storeEmployee(StoreEmployeeRequest $request): RedirectResponse {
        $this->createEmployeeAction->execute($request->validated());

        return back()->with('status', 'Ficha de empleado creada correctamente.');
    }

    public function storeDependent(StoreEmployeeDependentRequest $request): RedirectResponse {
        $employee = Employee::query()->findOrFail((int) $request->validated('employee_id'));
        $payload = $request->safe()->except(['employee_id']);

        $this->registerEmployeeDependentAction->execute($employee, $payload);

        return back()->with('status', 'Dependiente registrado correctamente.');
    }

    public function storeDisability(StoreEmployeeDisabilityRequest $request): RedirectResponse {
        $employee = Employee::query()->findOrFail((int) $request->validated('employee_id'));
        $payload = $request->safe()->except(['employee_id']);

        $this->registerEmployeeDisabilityAction->execute($employee, $payload);

        return back()->with('status', 'Discapacidad registrada correctamente.');
    }

    public function storeDisease(StoreEmployeeDiseaseRequest $request): RedirectResponse {
        $employee = Employee::query()->findOrFail((int) $request->validated('employee_id'));
        $payload = $request->safe()->except(['employee_id']);

        $this->registerEmployeeDiseaseAction->execute($employee, $payload);

        return back()->with('status', 'Enfermedad registrada correctamente.');
    }

    public function import(ImportEmployeesRequest $request): RedirectResponse {
        $file = $request->file('file');
        $result = $file === null ? ['imported' => 0, 'skipped' => 0, 'errors' => ['Archivo no encontrado.']] : $this->importEmployeesAction->execute($file);

        return back()
            ->with('status', "Importación completada: {$result['imported']} importados, {$result['skipped']} omitidos.")
            ->with('import_errors', $result['errors']);
    }

    private function ensureAdministrator(Request $request): void {
        /** @var User|null $user */
        $user = $request->user();

        abort_if($user === null || !$user->hasRole('Administrador'), 403);
    }
}
