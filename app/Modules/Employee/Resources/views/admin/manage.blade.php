@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Gestión de empleados y bienestar" />

    <div class="space-y-6">
        @if (session('status'))
            <x-ui.alert variant="success" :message="session('status')" />
        @endif

        @php
            $importErrors = session('import_errors', []);
        @endphp

        @if (is_array($importErrors) && count($importErrors) > 0)
            <x-ui.alert variant="warning" title="Filas omitidas en importación">
                <ul class="mt-2 list-disc pl-5 text-sm text-gray-500 dark:text-gray-400">
                    @foreach ($importErrors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        @if ($errors->any())
            <x-ui.alert variant="error" title="Se encontraron errores de validación">
                <ul class="mt-2 list-disc pl-5 text-sm text-gray-500 dark:text-gray-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <x-common.component-card title="Ficha del empleado" desc="Crear datos laborales y vinculación organizacional.">
                <form method="POST" action="{{ route('employee.admin.employees.store') }}" class="grid grid-cols-1 gap-3">
                    @csrf

                    <input name="employee_number" type="text" placeholder="Número de empleado (opcional)"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />

                    <select name="user_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Usuario</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>

                    <div class="grid grid-cols-2 gap-3">
                        <input name="username" type="text" required placeholder="Username"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="email" type="email" required placeholder="Correo"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <input name="first_name" type="text" required placeholder="Nombre"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="last_name" type="text" required placeholder="Apellido"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <input name="birth_date" type="date" required
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="hire_date" type="date" required
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <select name="position_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Cargo</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->title }}</option>
                            @endforeach
                        </select>

                        <select name="department_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Departamento (opcional)</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <select name="employment_status_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Estado laboral</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>

                        <select name="township_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Corregimiento</option>
                            @foreach ($townships as $township)
                                <option value="{{ $township->id }}">{{ $township->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <select name="parent_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Jefe directo (opcional)</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>

                        <select name="team_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Equipo (opcional)</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input name="salary" type="number" step="0.01" min="0" placeholder="Salario (opcional)"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />

                    <textarea name="address" rows="2" placeholder="Dirección (opcional)"
                              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>

                    <x-ui.button type="submit">Guardar ficha</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Carga masiva (CSV)" desc="Importar empleados con validación por fila (UC-ADM-07).">
                <form method="POST" action="{{ route('employee.admin.employees.import') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf

                    <input name="file" type="file" accept=".csv,text/csv"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-500 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-brand-600" />

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Columnas mínimas: user_id, username, first_name, last_name, email, birth_date, township_id, position_id, employment_status_id, hire_date.
                    </p>

                    <x-ui.button type="submit">Importar CSV</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <x-common.component-card title="Registrar dependiente" desc="UC-ADM-10: información familiar.">
                                <form method="POST" action="{{ route('employee.admin.employees.dependents.store') }}" class="space-y-3">
                    @csrf
                    <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Empleado</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                    <input name="first_name" type="text" required placeholder="Nombre"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <input name="last_name" type="text" required placeholder="Apellido"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <input name="relationship" type="text" required placeholder="Parentesco"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <input name="birth_date" type="date" required
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <x-ui.button type="submit">Guardar dependiente</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Registrar discapacidad" desc="UC-ADM-10: salud ocupacional.">
                                <form method="POST" action="{{ route('employee.admin.employees.disabilities.store') }}" class="space-y-3">
                    @csrf
                    <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Empleado</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                    <select name="disability_type_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Tipo de discapacidad</option>
                        @foreach ($disabilityTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <input name="diagnosis_date" type="date"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <textarea name="description" rows="2" placeholder="Descripción (opcional)"
                              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>
                    <x-ui.button type="submit">Guardar discapacidad</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Registrar enfermedad" desc="UC-ADM-10: enfermedad crónica.">
                                <form method="POST" action="{{ route('employee.admin.employees.diseases.store') }}" class="space-y-3">
                    @csrf
                    <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Empleado</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                    <select name="disease_type_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Tipo de enfermedad</option>
                        @foreach ($diseaseTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    <input name="diagnosis_date" type="date" required
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <textarea name="description" rows="2" placeholder="Descripción (opcional)"
                              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>
                    <x-ui.button type="submit">Guardar enfermedad</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <x-common.component-card title="Empleados registrados" desc="Resumen de ficha laboral actual.">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Empleado</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Cargo</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Departamento</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $employee->position?->title ?? 'Sin cargo' }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $employee->position?->department?->name ?? 'Sin departamento' }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $employee->is_active ? 'Activo' : 'Inactivo' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">No hay empleados registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>
@endsection
