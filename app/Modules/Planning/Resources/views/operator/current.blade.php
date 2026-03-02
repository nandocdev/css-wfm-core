@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mi horario actual" />

    <div class="space-y-6">
        @if ($weeklySchedule === null || $assignment === null)
            <x-ui.alert variant="info" title="Sin planificación publicada">
                No existe una planificación semanal publicada para tu usuario en la semana actual.
            </x-ui.alert>
        @else
            <x-common.component-card title="Horario de la semana" desc="UC-OP-02: consumo operativo de planificación publicada.">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Semana</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-white/90">
                            {{ $weeklySchedule->week_start_date?->format('d/m/Y') }} - {{ $weeklySchedule->week_end_date?->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Horario asignado</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-white/90">
                            {{ $assignment->schedule?->name }} ({{ $assignment->schedule?->start_time }} - {{ $assignment->schedule?->end_time }})
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Plantilla de descansos</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $assignment->breakTemplate?->name ?? 'Sin plantilla específica' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado semana</p>
                        <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $weeklySchedule->status }}</p>
                    </div>
                </div>
            </x-common.component-card>
        @endif
    </div>
@endsection
