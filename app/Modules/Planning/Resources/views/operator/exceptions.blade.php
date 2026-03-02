@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mis excepciones" />

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-common.component-card title="Permisos aprobados" desc="UC-OP-04: solicitudes aprobadas.">
            <div class="space-y-3">
                @forelse ($leaveRequests as $leave)
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $leave->incidentType?->name ?? 'Permiso' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $leave->start_datetime?->format('d/m/Y H:i') }} - {{ $leave->end_datetime?->format('d/m/Y H:i') }}
                        </p>
                        @if ($leave->justification)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $leave->justification }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin permisos aprobados.</p>
                @endforelse
            </div>
        </x-common.component-card>

        <x-common.component-card title="Incidencias justificadas" desc="UC-OP-04: incidencias de asistencia justificadas.">
            <div class="space-y-3">
                @forelse ($attendanceIncidents as $incident)
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $incident->incidentType?->name ?? 'Incidencia' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $incident->incident_date?->format('d/m/Y') }}</p>
                        @if ($incident->justification)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $incident->justification }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sin incidencias justificadas.</p>
                @endforelse
            </div>
        </x-common.component-card>
    </div>
@endsection
