@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mi perfil laboral" />

    <div class="space-y-6">
        @if ($employee === null)
            <x-ui.alert variant="warning" title="Sin información laboral">
                Tu usuario aún no tiene una ficha de empleado asociada.
            </x-ui.alert>
        @else
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-common.component-card title="Ficha laboral" desc="UC-OP-01: información laboral y vinculación organizacional.">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Empleado</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Número</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $employee->employee_number ?? 'No definido' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Cargo</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $employee->position?->title ?? 'Sin cargo' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Departamento</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $employee->position?->department?->name ?? 'Sin departamento' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Dirección</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $employee->position?->department?->directorate?->name ?? 'Sin dirección' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado laboral</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $employee->employmentStatus?->name ?? 'Sin estado' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Jefe directo</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90">
                                {{ $employee->parent?->first_name ? $employee->parent->first_name.' '.$employee->parent->last_name : 'No asignado' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Fecha de ingreso</p>
                            <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $employee->hire_date?->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </x-common.component-card>

                <x-common.component-card title="Equipos" desc="Vinculación operativa actual.">
                    <div class="space-y-2">
                        @forelse ($employee->teamMemberships as $membership)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $membership->team?->name ?? 'Equipo no disponible' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Desde {{ $membership->start_date?->format('d/m/Y') }}
                                    @if ($membership->end_date)
                                        hasta {{ $membership->end_date->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sin equipo asignado.</p>
                        @endforelse
                    </div>
                </x-common.component-card>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <x-common.component-card title="Dependientes" desc="UC-OP-13: grupo familiar registrado.">
                    <div class="space-y-2">
                        @forelse ($employee->dependents as $dependent)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $dependent->first_name }} {{ $dependent->last_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $dependent->relationship }} · {{ $dependent->birth_date?->format('d/m/Y') }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sin información registrada.</p>
                        @endforelse
                    </div>
                </x-common.component-card>

                <x-common.component-card title="Discapacidades" desc="UC-OP-13: condiciones de salud activas.">
                    <div class="space-y-2">
                        @forelse ($employee->disabilities->where('is_active', true) as $disability)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $disability->disabilityType?->name ?? 'Tipo no definido' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $disability->diagnosis_date?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sin información registrada.</p>
                        @endforelse
                    </div>
                </x-common.component-card>

                <x-common.component-card title="Enfermedades" desc="UC-OP-13: historial de enfermedades crónicas.">
                    <div class="space-y-2">
                        @forelse ($employee->diseases->where('is_active', true) as $disease)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $disease->diseaseType?->name ?? 'Tipo no definido' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $disease->diagnosis_date?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sin información registrada.</p>
                        @endforelse
                    </div>
                </x-common.component-card>
            </div>
        @endif
    </div>
@endsection
