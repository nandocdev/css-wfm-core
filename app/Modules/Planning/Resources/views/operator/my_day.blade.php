@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mi Día" />

    <div class="space-y-6">
        @php
            $schedule = $assignment?->schedule;
            $templateBreaks = $assignment?->breakTemplate;
        @endphp

        @if ($assignment === null && $intradayAssignments->isEmpty())
            <x-ui.alert variant="info" title="Sin actividades intradía">
                No tienes actividades ni turnos publicados para hoy.
            </x-ui.alert>
        @else
            <x-common.component-card title="Timeline diario" desc="UC-OP-12: turno base, pausas activas y actividades intradía.">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Franja</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Tipo</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @if ($schedule)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ substr((string) $schedule->start_time, 0, 5) }} -
                                        {{ substr((string) $schedule->end_time, 0, 5) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Turno</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $schedule->name }}
                                        @if ($weeklySchedule)
                                            · Semana {{ $weeklySchedule->week_start_date?->format('d/m/Y') }} - {{ $weeklySchedule->week_end_date?->format('d/m/Y') }}
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @php
                                $activeBreaks = $breakOverride;
                            @endphp

                            @if ($activeBreaks)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ substr((string) $activeBreaks->break_start, 0, 5) }} - {{ substr((string) $activeBreaks->break_end, 0, 5) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Pausa activa</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Break sobrescrito por coordinación</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ substr((string) $activeBreaks->lunch_start, 0, 5) }} - {{ substr((string) $activeBreaks->lunch_end, 0, 5) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Pausa activa</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Almuerzo sobrescrito por coordinación</td>
                                </tr>
                            @elseif ($templateBreaks)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ substr((string) $templateBreaks->break_start, 0, 5) }} - {{ substr((string) $templateBreaks->break_end, 0, 5) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Pausa activa</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Break desde plantilla semanal</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ substr((string) $templateBreaks->lunch_start, 0, 5) }} - {{ substr((string) $templateBreaks->lunch_end, 0, 5) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Pausa activa</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Almuerzo desde plantilla semanal</td>
                                </tr>
                            @endif

                            @forelse ($intradayAssignments as $activityAssignment)
                                @php $activity = $activityAssignment->intradayActivity; @endphp
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ substr((string) $activity?->start_time, 0, 5) }} - {{ substr((string) $activity?->end_time, 0, 5) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">Actividad intradía</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $activity?->name }}
                                        @if ($activity?->notes)
                                            · {{ $activity->notes }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Sin actividades intradía asignadas para hoy.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif
    </div>
@endsection
