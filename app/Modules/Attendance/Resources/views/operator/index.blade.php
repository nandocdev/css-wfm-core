@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mi asistencia" />

    <div class="space-y-6">
        @if ($employee === null)
            <x-ui.alert variant="warning" title="Sin empleado asociado">
                No existe un empleado activo vinculado a tu usuario para consultar asistencia.
            </x-ui.alert>
        @else
            <x-common.component-card title="Asistencia de hoy" desc="UC-OP-10: detalle diario de incidencias registradas.">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Tipo</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Franja</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Registrado por</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($todayIncidents as $incident)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->incidentType?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        @if ($incident->start_time && $incident->end_time)
                                            {{ substr((string) $incident->start_time, 0, 5) }} - {{ substr((string) $incident->end_time, 0, 5) }}
                                        @else
                                            Día completo
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->recordedBy?->first_name }} {{ $incident->recordedBy?->last_name }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->justification ?? 'Sin observaciones' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Sin incidencias registradas para hoy.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>

            <x-common.component-card title="Historial de asistencia" desc="UC-OP-11: consulta por rango de fechas.">
                <form method="GET" action="{{ route('attendance.operator.index') }}" class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <input name="from_date" type="date" value="{{ $fromDate }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <input name="to_date" type="date" value="{{ $toDate }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <x-ui.button type="submit">Filtrar historial</x-ui.button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Fecha</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Tipo</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Franja</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($historyIncidents as $incident)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->incident_date?->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->incidentType?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        @if ($incident->start_time && $incident->end_time)
                                            {{ substr((string) $incident->start_time, 0, 5) }} - {{ substr((string) $incident->end_time, 0, 5) }}
                                        @else
                                            Día completo
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->justification ?? 'Sin observaciones' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Sin incidencias para el rango seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif
    </div>
@endsection
