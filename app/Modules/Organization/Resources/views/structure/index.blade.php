@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Estructura organizacional" />

    <div class="space-y-6">
        @if (session('status'))
            <x-ui.alert variant="success" :message="session('status')" />
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

        @if ($canManageStructure)
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <x-common.component-card title="Crear dirección" desc="Nivel superior de la estructura corporativa.">
                    <form method="POST" action="{{ route('organization.directorates.store') }}" class="space-y-3">
                        @csrf
                        <input name="name" type="text" required placeholder="Nombre de dirección"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <textarea name="description" rows="3" placeholder="Descripción (opcional)"
                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>
                        <x-ui.button type="submit">Guardar dirección</x-ui.button>
                    </form>
                </x-common.component-card>

                <x-common.component-card title="Crear departamento" desc="Asociado a una dirección existente.">
                    <form method="POST" action="{{ route('organization.departments.store') }}" class="space-y-3">
                        @csrf
                        <input name="name" type="text" required placeholder="Nombre de departamento"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <select name="directorate_id" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Selecciona dirección</option>
                            @foreach ($directorates as $directorate)
                                <option value="{{ $directorate->id }}">{{ $directorate->name }}</option>
                            @endforeach
                        </select>
                        <textarea name="description" rows="3" placeholder="Descripción (opcional)"
                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>
                        <x-ui.button type="submit">Guardar departamento</x-ui.button>
                    </form>
                </x-common.component-card>

                <x-common.component-card title="Crear cargo" desc="Asociado a un departamento existente.">
                    <form method="POST" action="{{ route('organization.positions.store') }}" class="space-y-3">
                        @csrf
                        <input name="title" type="text" required placeholder="Título del cargo"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="position_code" type="text" required placeholder="Código del cargo"
                               class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <select name="department_id" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Selecciona departamento</option>
                            @foreach ($directorates as $directorate)
                                @foreach ($directorate->departments as $department)
                                    <option value="{{ $department->id }}">{{ $directorate->name }} / {{ $department->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        <textarea name="description" rows="3" placeholder="Descripción (opcional)"
                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>
                        <x-ui.button type="submit">Guardar cargo</x-ui.button>
                    </form>
                </x-common.component-card>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <x-common.component-card title="Mapa corporativo" desc="Relación Directorate → Department → Position.">
                <div class="space-y-4">
                    @forelse ($directorates as $directorate)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <h4 class="font-semibold text-gray-800 dark:text-white/90">{{ $directorate->name }}</h4>
                            @if ($directorate->description)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $directorate->description }}</p>
                            @endif

                            <div class="mt-3 space-y-3 pl-3">
                                @forelse ($directorate->departments as $department)
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $department->name }}</p>
                                        <ul class="mt-1 list-disc pl-5 text-sm text-gray-500 dark:text-gray-400">
                                            @forelse ($department->positions as $position)
                                                <li>{{ $position->title }} ({{ $position->position_code }})</li>
                                            @empty
                                                <li>Sin cargos registrados.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin departamentos registrados.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay direcciones registradas.</p>
                    @endforelse
                </div>
            </x-common.component-card>

            <x-common.component-card title="Jerarquía de empleados" desc="Vista recursiva para validación de descendencia (UC-INT-04 / UC-JEF-01).">
                <div class="space-y-2">
                    @php
                        $renderEmployeeNode = function ($employee, $depth = 0) use (&$renderEmployeeNode) {
                            $indent = $depth * 18;
                            $positionTitle = $employee->position?->title ?? 'Sin cargo';
                            $departmentName = $employee->position?->department?->name ?? ($employee->department?->name ?? 'Sin departamento');
                            $directorateName = $employee->position?->department?->directorate?->name ?? 'Sin dirección';

                            echo '<div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800" style="margin-left: '.$indent.'px">';
                            echo '<p class="text-sm font-semibold text-gray-800 dark:text-white/90">'.e($employee->first_name.' '.$employee->last_name).'</p>';
                            echo '<p class="text-xs text-gray-500 dark:text-gray-400">'.e($positionTitle).' · '.e($departmentName).' · '.e($directorateName).'</p>';
                            echo '</div>';

                            foreach ($employee->descendants as $child) {
                                $renderEmployeeNode($child, $depth + 1);
                            }
                        };
                    @endphp

                    @forelse ($hierarchyRoots as $rootEmployee)
                        {!! $renderEmployeeNode($rootEmployee, 0) !!}
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay jerarquía de empleados registrada.</p>
                    @endforelse
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection
