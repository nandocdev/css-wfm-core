@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Intelligence & Persistence" />

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

        @if ($employee === null)
            <x-ui.alert variant="warning" title="Sin empleado asociado">
                No existe un empleado activo vinculado a tu usuario.
            </x-ui.alert>
        @else
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-common.component-card title="Resolver horario efectivo" desc="UC-INT-07/08: prioridad Excepción > Intradía > Semanal.">
                    <form method="POST" action="{{ route('intelligence.schedule.resolve') }}" class="space-y-3">
                        @csrf
                        <input name="employee_id" type="number" min="1" required placeholder="ID de empleado"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="date" type="date" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <x-ui.button type="submit">Resolver prioridad</x-ui.button>
                    </form>

                    @if (session('resolved_schedule'))
                        @php($resolved = session('resolved_schedule'))
                        <div class="mt-4 rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">Fuente activa: {{ $resolved['source_label'] ?? 'N/D' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Fecha: {{ $resolved['date'] ?? 'N/D' }} · Empleado: {{ $resolved['employee_id'] ?? 'N/D' }}</p>
                            @if (!empty($resolved['details']))
                                <ul class="mt-2 list-disc pl-5 text-xs text-gray-500 dark:text-gray-400">
                                    @foreach ($resolved['details'] as $key => $value)
                                        <li>{{ $key }}: {{ is_scalar($value) || $value === null ? (string) $value : json_encode($value) }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </x-common.component-card>

                @if ($canReprocess)
                    <x-common.component-card title="Mantenimiento: reprocesamiento" desc="UC-ADM-08: recalcular y auditar resolución por fecha/empleado.">
                        <form method="POST" action="{{ route('intelligence.maintenance.reprocess') }}" class="space-y-3">
                            @csrf
                            <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Empleado</option>
                                @foreach ($activeEmployees as $targetEmployee)
                                    <option value="{{ $targetEmployee->id }}">{{ $targetEmployee->first_name }} {{ $targetEmployee->last_name }}</option>
                                @endforeach
                            </select>
                            <input name="date" type="date" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                            <x-ui.button type="submit">Ejecutar reprocesamiento</x-ui.button>
                        </form>
                    </x-common.component-card>
                @endif
            </div>

            @if ($canForceApprove)
                <x-common.component-card title="WFM Advanced: Aprobaciones institucionales" desc="UC-WFM-06 / UC-DIR-04 / UC-JEF-06: forzar aprobación con trazabilidad.">
                    <div class="space-y-3">
                        @forelse ($pendingInstitutionalExceptions as $pending)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $pending->employee?->first_name }} {{ $pending->employee?->last_name }} · {{ $pending->incidentType?->name ?? 'Permiso' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $pending->start_datetime?->format('d/m/Y H:i') }} - {{ $pending->end_datetime?->format('d/m/Y H:i') }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $pending->justification }}</p>

                                <form method="POST" action="{{ route('intelligence.exceptions.force_approve', $pending) }}" class="mt-3 space-y-2">
                                    @csrf
                                    <textarea name="justification" rows="2" required placeholder="Motivo de aprobación institucional forzada"
                                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-900"></textarea>
                                    <x-ui.button type="submit">Forzar aprobación</x-ui.button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No hay excepciones institucionales pendientes.</p>
                        @endforelse
                    </div>
                </x-common.component-card>
            @endif
        @endif
    </div>
@endsection
