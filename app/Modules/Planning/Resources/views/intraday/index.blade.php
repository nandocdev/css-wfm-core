@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Planificación intradía" />

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
            <x-common.component-card title="Crear actividad intradía" desc="UC-WFM-10: capacitaciones, reuniones y coaching.">
                <form method="POST" action="{{ route('planning.intraday.store') }}" class="space-y-3">
                    @csrf
                    <select name="weekly_schedule_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Semana objetivo</option>
                        @foreach ($weeklySchedules as $weekly)
                            <option value="{{ $weekly->id }}">#{{ $weekly->id }} · {{ $weekly->week_start_date?->format('d/m/Y') }} a {{ $weekly->week_end_date?->format('d/m/Y') }} ({{ $weekly->status }})</option>
                        @endforeach
                    </select>

                    <input name="name" type="text" required maxlength="120" placeholder="Nombre de la actividad"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />

                    <input name="activity_date" type="date" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <input name="start_time" type="time" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="end_time" type="time" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>

                    <input name="max_participants" type="number" min="1" max="500" placeholder="Cupo máximo (opcional)"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />

                    <textarea name="notes" rows="3" placeholder="Notas (opcional)"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>

                    <x-ui.button type="submit">Crear actividad</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Asignar operadores" desc="UC-WFM-11: asignación con validación de cupo y conflictos.">
                <form method="POST" action="{{ route('planning.intraday.assign') }}" class="space-y-3">
                    @csrf
                    <select name="intraday_activity_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Actividad intradía</option>
                        @foreach ($intradayActivities as $activity)
                            <option value="{{ $activity->id }}">
                                #{{ $activity->id }} · {{ $activity->name }} · {{ $activity->activity_date?->format('d/m/Y') }} ·
                                {{ substr((string) $activity->start_time, 0, 5) }} - {{ substr((string) $activity->end_time, 0, 5) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="employee_ids[]" multiple required class="min-h-40 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>

                    <x-ui.button type="submit">Asignar operadores</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <x-common.component-card title="Actividades intradía registradas" desc="Seguimiento de cupo y operadores asignados.">
            <div class="space-y-4">
                @forelse ($intradayActivities as $activity)
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                    {{ $activity->name }} · {{ $activity->activity_date?->format('d/m/Y') }} ·
                                    {{ substr((string) $activity->start_time, 0, 5) }} - {{ substr((string) $activity->end_time, 0, 5) }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Semana #{{ $activity->weekly_schedule_id }} · Cupo:
                                    {{ $activity->max_participants ?? 'Sin límite' }} · Asignados: {{ $activity->assignments->count() }}
                                </p>
                                @if ($activity->notes)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $activity->notes }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Operador</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Usuario</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse ($activity->assignments as $assignment)
                                        <tr>
                                            <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $assignment->employee?->first_name }} {{ $assignment->employee?->last_name }}
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $assignment->employee?->user?->email ?? 'N/A' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Sin operadores asignados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No existen actividades intradía registradas.</p>
                @endforelse
            </div>
        </x-common.component-card>
    </div>
@endsection
