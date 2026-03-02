@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mi equipo" />

    <div class="space-y-6">
        @if ($team === null)
            <x-ui.alert variant="warning" title="Equipo no asignado">
                No tienes un equipo directo asignado. Contacta al administrador del sistema.
            </x-ui.alert>
        @else
            <x-common.component-card title="Equipo directo" desc="UC-COOR-01: personal bajo tu administración.">
                <div class="mb-4 rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $team->name }}</h4>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $team->description ?? 'Sin descripción registrada.' }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Empleado</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Cargo</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Departamento</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Dirección</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($team->activeMembers as $member)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $member->employee?->first_name }} {{ $member->employee?->last_name }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $member->employee?->position?->title ?? 'Sin cargo' }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $member->employee?->position?->department?->name ?? 'Sin departamento' }}
                                    </td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $member->employee?->position?->department?->directorate?->name ?? 'Sin dirección' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        No hay miembros activos asignados a tu equipo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif
    </div>
@endsection
