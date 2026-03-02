@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Analytics & Monitoring" />

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

        <x-common.component-card title="Filtros globales" desc="Aplica rango temporal para KPIs, reportes y monitoreo de solo lectura.">
            <form method="GET" action="{{ route('analytics.monitoring.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                <x-ui.button type="submit">Aplicar filtros</x-ui.button>
            </form>
        </x-common.component-card>

        @if ($canExecutive)
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
                <x-common.component-card title="Empleados activos">
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $globalKpis['active_employees'] }}</p>
                </x-common.component-card>
                <x-common.component-card title="Ausentes">
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $globalKpis['absent_employees'] }}</p>
                </x-common.component-card>
                <x-common.component-card title="Cubiertos">
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $globalKpis['covered_employees'] }}</p>
                </x-common.component-card>
                <x-common.component-card title="% Ausentismo">
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $globalKpis['absenteeism_rate'] }}%</p>
                </x-common.component-card>
                <x-common.component-card title="% Cobertura">
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $globalKpis['coverage_rate'] }}%</p>
                </x-common.component-card>
            </div>

            <x-common.component-card title="Dashboard Director/Jefe" desc="UC-DIR-01/02: KPIs de ausentismo y cobertura por dirección.">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2">Dirección</th>
                                <th class="px-3 py-2">Dotación</th>
                                <th class="px-3 py-2">Ausentes</th>
                                <th class="px-3 py-2">Cobertura %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($executiveRows as $row)
                                <tr class="border-t border-gray-200 dark:border-gray-800">
                                    <td class="px-3 py-2">{{ $row['directorate'] }}</td>
                                    <td class="px-3 py-2">{{ $row['headcount'] }}</td>
                                    <td class="px-3 py-2">{{ $row['absent_employees'] }}</td>
                                    <td class="px-3 py-2">{{ $row['coverage_percent'] }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-3 text-gray-500 dark:text-gray-400" colspan="4">Sin datos para el rango seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif

        @if ($canManagement)
            <x-common.component-card title="Reportería consolidada por Jefatura" desc="UC-JEF-05: indicadores por departamento para toma de decisiones.">
                <div class="mb-3 flex flex-wrap gap-2">
                    @if ($canExport)
                        <a href="{{ route('analytics.monitoring.export', ['report_type' => 'management', 'format' => 'csv', 'start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="inline-flex rounded-lg border border-gray-300 px-3 py-2 text-xs dark:border-gray-700">Exportar CSV</a>
                        <a href="{{ route('analytics.monitoring.export', ['report_type' => 'management', 'format' => 'excel', 'start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="inline-flex rounded-lg border border-gray-300 px-3 py-2 text-xs dark:border-gray-700">Exportar Excel</a>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2">Dirección</th>
                                <th class="px-3 py-2">Departamento</th>
                                <th class="px-3 py-2">Dotación</th>
                                <th class="px-3 py-2">Permisos aprobados</th>
                                <th class="px-3 py-2">Incidencias</th>
                                <th class="px-3 py-2">Incidencias x100</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($managementRows as $row)
                                <tr class="border-t border-gray-200 dark:border-gray-800">
                                    <td class="px-3 py-2">{{ $row['directorate'] }}</td>
                                    <td class="px-3 py-2">{{ $row['department'] }}</td>
                                    <td class="px-3 py-2">{{ $row['headcount'] }}</td>
                                    <td class="px-3 py-2">{{ $row['approved_leaves'] }}</td>
                                    <td class="px-3 py-2">{{ $row['incidents'] }}</td>
                                    <td class="px-3 py-2">{{ $row['incidents_per_100'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-3 text-gray-500 dark:text-gray-400" colspan="6">Sin datos para el rango seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif

        @if ($canCoordinator)
            <x-common.component-card title="Reporte de cumplimiento por Coordinación" desc="UC-COOR-08: cumplimiento operativo por equipo.">
                <div class="mb-3 flex flex-wrap gap-2">
                    @if ($canExport)
                        <a href="{{ route('analytics.monitoring.export', ['report_type' => 'coordinator', 'format' => 'csv', 'start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="inline-flex rounded-lg border border-gray-300 px-3 py-2 text-xs dark:border-gray-700">Exportar CSV</a>
                        <a href="{{ route('analytics.monitoring.export', ['report_type' => 'coordinator', 'format' => 'excel', 'start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="inline-flex rounded-lg border border-gray-300 px-3 py-2 text-xs dark:border-gray-700">Exportar Excel</a>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-3 py-2">Equipo</th>
                                <th class="px-3 py-2">Coordinador</th>
                                <th class="px-3 py-2">Dotación</th>
                                <th class="px-3 py-2">Afectados</th>
                                <th class="px-3 py-2">Cumplimiento %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($coordinatorRows as $row)
                                <tr class="border-t border-gray-200 dark:border-gray-800">
                                    <td class="px-3 py-2">{{ $row['team'] }}</td>
                                    <td class="px-3 py-2">{{ $row['coordinator'] }}</td>
                                    <td class="px-3 py-2">{{ $row['headcount'] }}</td>
                                    <td class="px-3 py-2">{{ $row['affected_members'] }}</td>
                                    <td class="px-3 py-2">{{ $row['compliance_percent'] }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-3 text-gray-500 dark:text-gray-400" colspan="5">Sin datos para el rango seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif

        @if ($canReadonly)
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-common.component-card title="Monitoreo solo lectura: incidencias" desc="UC-SUP-01/02 y UC-COOR-09.">
                    <div class="space-y-2">
                        @forelse ($readonlyMonitoring['incidents'] as $incident)
                            <div class="rounded-lg border border-gray-200 p-3 text-xs dark:border-gray-800">
                                {{ $incident['date'] }} · {{ $incident['employee'] }} · {{ $incident['type'] }}
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sin incidencias en el rango seleccionado.</p>
                        @endforelse
                    </div>
                </x-common.component-card>

                <x-common.component-card title="Monitoreo solo lectura: solicitudes pendientes" desc="Bandeja informativa sin acciones operativas.">
                    <div class="space-y-2">
                        @forelse ($readonlyMonitoring['pending_leaves'] as $leave)
                            <div class="rounded-lg border border-gray-200 p-3 text-xs dark:border-gray-800">
                                {{ $leave['start'] }} - {{ $leave['end'] }} · {{ $leave['employee'] }} · {{ $leave['type'] }}
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Sin solicitudes pendientes.</p>
                        @endforelse
                    </div>
                </x-common.component-card>
            </div>
        @endif
    </div>
@endsection
