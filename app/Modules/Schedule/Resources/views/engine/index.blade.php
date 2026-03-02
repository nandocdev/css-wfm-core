@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Schedule Engine" />

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

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <x-common.component-card title="Crear horario" desc="UC-WFM-01: catálogo base con cálculo automático de jornada neta.">
                <form method="POST" action="{{ route('schedule.schedules.store') }}" class="space-y-3">
                    @csrf
                    <input name="name" type="text" required placeholder="Nombre de horario"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <div class="grid grid-cols-2 gap-3">
                        <input name="start_time" type="time" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="end_time" type="time" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input name="lunch_minutes" type="number" min="0" max="240" value="45" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="break_minutes" type="number" min="0" max="180" value="15" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>
                    <x-ui.button type="submit">Guardar horario</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Crear plantilla de descansos" desc="UC-WFM-12: administrar break templates por equipo.">
                <form method="POST" action="{{ route('schedule.break_templates.store') }}" class="space-y-3">
                    @csrf
                    <input name="name" type="text" required placeholder="Nombre de plantilla"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <select name="team_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Equipo</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <div class="grid grid-cols-2 gap-3">
                        <input name="lunch_start" type="time" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="lunch_end" type="time" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input name="break_start" type="time" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="break_end" type="time" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    </div>
                    <x-ui.button type="submit">Guardar plantilla</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Configuraciones WFM" desc="UC-WFM-03: tolerancias y umbrales globales.">
                <form method="POST" action="{{ route('schedule.wfm.settings.update') }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <input name="late_tolerance_minutes" type="number" min="0" max="180" required
                        value="{{ $wfmSetting->late_tolerance_minutes }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <input name="early_leave_tolerance_minutes" type="number" min="0" max="180" required
                        value="{{ $wfmSetting->early_leave_tolerance_minutes }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <input name="approval_threshold_hours" type="number" min="1" max="168" required
                        value="{{ $wfmSetting->approval_threshold_hours }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <input name="max_overtime_minutes" type="number" min="0" max="600" required
                        value="{{ $wfmSetting->max_overtime_minutes }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="allow_force_approval" value="1" @checked($wfmSetting->allow_force_approval)>
                        Permitir aprobación forzada
                    </label>
                    <x-ui.button type="submit">Guardar configuraciones</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <x-common.component-card title="Horarios registrados" desc="UC-WFM-02: edición y control de catálogo de horarios.">
                <div class="space-y-3">
                    @forelse ($schedules as $schedule)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <form method="POST" action="{{ route('schedule.schedules.update', $schedule) }}" class="grid grid-cols-1 gap-3">
                                @csrf
                                @method('PUT')
                                <input name="name" type="text" required value="{{ $schedule->name }}"
                                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                <div class="grid grid-cols-2 gap-3">
                                    <input name="start_time" type="time" required value="{{ substr((string) $schedule->start_time, 0, 5) }}"
                                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                    <input name="end_time" type="time" required value="{{ substr((string) $schedule->end_time, 0, 5) }}"
                                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <input name="lunch_minutes" type="number" min="0" max="240" required value="{{ $schedule->lunch_minutes }}"
                                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                    <input name="break_minutes" type="number" min="0" max="180" required value="{{ $schedule->break_minutes }}"
                                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Jornada neta: {{ $schedule->total_minutes }} min</p>
                                <label class="inline-flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <input type="checkbox" name="confirm_change" value="1">
                                    Confirmar cambio si el horario tiene asignaciones activas
                                </label>
                                <div class="flex gap-2">
                                    <x-ui.button type="submit">Actualizar</x-ui.button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('schedule.schedules.delete', $schedule) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit">Eliminar</x-ui.button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sin horarios registrados.</p>
                    @endforelse
                </div>
            </x-common.component-card>

            <x-common.component-card title="Plantillas y asignación semanal" desc="UC-WFM-12: vincular break template a asignaciones semanales.">
                <form method="POST" action="{{ route('schedule.break_templates.assign') }}" class="mb-4 grid grid-cols-1 gap-3">
                    @csrf
                    <select name="weekly_schedule_assignment_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Asignación semanal</option>
                        @foreach ($weeklyAssignments as $assignment)
                            <option value="{{ $assignment->id }}">
                                #{{ $assignment->id }} · {{ $assignment->employee?->first_name }} {{ $assignment->employee?->last_name }} · Semana {{ $assignment->weeklySchedule?->week_start_date }}
                            </option>
                        @endforeach
                    </select>
                    <select name="break_template_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Plantilla de descanso</option>
                        @foreach ($breakTemplates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->team?->name }})</option>
                        @endforeach
                    </select>
                    <x-ui.button type="submit">Asignar plantilla</x-ui.button>
                </form>

                <div class="space-y-3">
                    @forelse ($breakTemplates as $template)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <form method="POST" action="{{ route('schedule.break_templates.update', $template) }}" class="grid grid-cols-1 gap-2">
                                @csrf
                                @method('PUT')
                                <input name="name" type="text" value="{{ $template->name }}" required
                                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                <select name="team_id" required class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900">
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" @selected($team->id === $template->team_id)>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                <div class="grid grid-cols-2 gap-2">
                                    <input name="lunch_start" type="time" required value="{{ substr((string) $template->lunch_start, 0, 5) }}"
                                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                    <input name="lunch_end" type="time" required value="{{ substr((string) $template->lunch_end, 0, 5) }}"
                                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <input name="break_start" type="time" required value="{{ substr((string) $template->break_start, 0, 5) }}"
                                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                    <input name="break_end" type="time" required value="{{ substr((string) $template->break_end, 0, 5) }}"
                                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                </div>
                                <x-ui.button type="submit">Actualizar plantilla</x-ui.button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Sin plantillas registradas.</p>
                    @endforelse
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection
