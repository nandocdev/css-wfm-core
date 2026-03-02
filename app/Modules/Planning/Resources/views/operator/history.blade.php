@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Historial de horarios" />

    <x-common.component-card title="Historial" desc="UC-OP-03: semanas publicadas anteriores.">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Semana</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Horario</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Plantilla descanso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($history as $assignment)
                        <tr>
                            <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $assignment->weeklySchedule?->week_start_date?->format('d/m/Y') }} - {{ $assignment->weeklySchedule?->week_end_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $assignment->schedule?->name ?? 'Sin horario' }}
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $assignment->breakTemplate?->name ?? 'Sin plantilla' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Sin historial disponible.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-common.component-card>
@endsection
