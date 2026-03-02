@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Planificación semanal (WFM)" />

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

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <x-common.component-card title="Crear semana draft" desc="UC-WFM-04: iniciar planificación semanal.">
                <form method="POST" action="{{ route('planning.weekly.store') }}" class="space-y-3">
                    @csrf
                    <input name="week_start_date" type="date" required
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">La fecha debe corresponder a un lunes.</p>
                    <x-ui.button type="submit">Crear draft</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Motor de asignación masiva" desc="UC-WFM-04: asignar turnos base por equipo o selección manual.">
                                <form method="POST" action="{{ route('planning.weekly.assign.mass') }}" class="space-y-3">
                    @csrf
                    <select name="weekly_schedule_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Semana draft</option>
                        @foreach ($weeklySchedules->where('status', 'draft') as $weekly)
                            <option value="{{ $weekly->id }}">#{{ $weekly->id }} · {{ $weekly->week_start_date?->format('d/m/Y') }} a {{ $weekly->week_end_date?->format('d/m/Y') }}</option>
                        @endforeach
                    </select>

                    <select name="schedule_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Horario base</option>
                        @foreach ($schedules as $schedule)
                            <option value="{{ $schedule->id }}">{{ $schedule->name }} ({{ $schedule->start_time }} - {{ $schedule->end_time }})</option>
                        @endforeach
                    </select>

                    <select name="break_template_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Plantilla de descansos (opcional)</option>
                        @foreach ($breakTemplates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>

                    <select name="team_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Equipo (opcional)</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>

                    <select name="employee_ids[]" multiple class="min-h-28 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>

                    <x-ui.button type="submit">Ejecutar asignación masiva</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <x-common.component-card title="Grilla semanal (estado draft/publicado)" desc="UC-WFM-04/09: gestión de asignaciones y publicación.">
            <div class="space-y-4">
                @forelse ($weeklySchedules as $weekly)
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                    Semana #{{ $weekly->id }} · {{ $weekly->week_start_date?->format('d/m/Y') }} - {{ $weekly->week_end_date?->format('d/m/Y') }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Estado: {{ $weekly->status }}
                                    @if ($weekly->published_at)
                                        · Publicado: {{ $weekly->published_at->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                            </div>

                            @if ($weekly->status === 'draft')
                                <form method="POST" action="{{ route('planning.weekly.publish', $weekly) }}">
                                    @csrf
                                    <x-ui.button type="submit">Publicar planificación</x-ui.button>
                                </form>
                            @endif
                        </div>

                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Empleado</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Horario</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Plantilla descanso</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse ($weekly->assignments as $assignment)
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $assignment->employee?->first_name }} {{ $assignment->employee?->last_name }}
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
                                            <td colspan="3" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Sin asignaciones.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No existen planificaciones semanales.</p>
                @endforelse
            </div>
        </x-common.component-card>
    </div>
@endsection
