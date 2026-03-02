@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Incidencias de asistencia" />

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

        @if ($team === null)
            <x-ui.alert variant="warning" title="Equipo no disponible">
                No tienes un equipo activo asignado para registrar incidencias.
            </x-ui.alert>
        @else
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-common.component-card title="Registrar incidencia" desc="UC-COOR-02: registro por colaborador del equipo.">
                    <form method="POST" action="{{ route('attendance.coordinator.incidents.store') }}" class="space-y-3">
                        @csrf
                        <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Colaborador del equipo</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->employee_id }}">{{ $member->employee?->first_name }} {{ $member->employee?->last_name }}</option>
                            @endforeach
                        </select>

                        <select name="incident_type_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Tipo de incidencia</option>
                            @foreach ($incidentTypes as $incidentType)
                                <option value="{{ $incidentType->id }}">{{ $incidentType->name }}</option>
                            @endforeach
                        </select>

                        <input name="incident_date" type="date" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input name="start_time" type="time"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                            <input name="end_time" type="time"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        </div>

                        <textarea name="justification" rows="3" placeholder="Observaciones o justificación (opcional)"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>

                        <x-ui.button type="submit">Registrar incidencia</x-ui.button>
                    </form>
                </x-common.component-card>

                <x-common.component-card title="Mi equipo" desc="Solo se permiten colaboradores del mismo team_id.">
                    <div class="space-y-2">
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-medium">Equipo:</span> {{ $team->name }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-medium">Miembros activos:</span> {{ $members->count() }}</p>
                    </div>
                </x-common.component-card>
            </div>

            <x-common.component-card title="Incidencias recientes" desc="Historial del equipo administrado.">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Fecha</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Colaborador</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Tipo</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Franja</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Registrado por</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($incidents as $incident)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->incident_date?->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->employee?->first_name }} {{ $incident->employee?->last_name }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->incidentType?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        @if ($incident->start_time && $incident->end_time)
                                            {{ substr((string) $incident->start_time, 0, 5) }} - {{ substr((string) $incident->end_time, 0, 5) }}
                                        @else
                                            Día completo
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $incident->recordedBy?->first_name }} {{ $incident->recordedBy?->last_name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Sin incidencias registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif
    </div>
@endsection
