@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Gestión de pausas del equipo" />

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
                No tienes un equipo activo asignado para gestionar pausas.
            </x-ui.alert>
        @else
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-common.component-card title="Sobrescribir pausas" desc="UC-COOR-07: ajuste temporal de descansos por colaborador.">
                    <form method="POST" action="{{ route('planning.coordinator.breaks.store') }}" class="space-y-3">
                        @csrf
                        <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Selecciona colaborador del equipo</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->employee_id }}">{{ $member->employee?->first_name }} {{ $member->employee?->last_name }}</option>
                            @endforeach
                        </select>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input name="lunch_start" type="time" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                            <input name="lunch_end" type="time" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input name="break_start" type="time" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                            <input name="break_end" type="time" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        </div>

                        <textarea name="reason" rows="3" required placeholder="Motivo de la sobrescritura"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input name="effective_from" type="date" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                            <input name="effective_to" type="date" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        </div>

                        <x-ui.button type="submit">Guardar sobrescritura</x-ui.button>
                    </form>
                </x-common.component-card>

                <x-common.component-card title="Equipo directo" desc="Solo se permiten colaboradores del equipo asignado.">
                    <div class="space-y-2">
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-medium">Equipo:</span> {{ $team->name }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-medium">Miembros activos:</span> {{ $members->count() }}</p>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Colaborador</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Usuario</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($members as $member)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $member->employee?->first_name }} {{ $member->employee?->last_name }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $member->employee?->user?->email ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">No hay miembros activos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-common.component-card>
            </div>

            <x-common.component-card title="Sobrescrituras recientes" desc="Historial de ajustes de pausas del equipo.">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Colaborador</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Almuerzo</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Break</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Vigencia</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($overrides as $override)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $override->employee?->first_name }} {{ $override->employee?->last_name }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ substr((string) $override->lunch_start, 0, 5) }} - {{ substr((string) $override->lunch_end, 0, 5) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ substr((string) $override->break_start, 0, 5) }} - {{ substr((string) $override->break_end, 0, 5) }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $override->effective_from?->format('d/m/Y') }} - {{ $override->effective_to?->format('d/m/Y') ?? 'Abierta' }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $override->reason }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">Sin sobrescrituras registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif
    </div>
@endsection
