@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Gestión de equipos" />

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
            <x-common.component-card title="Crear equipo" desc="Gestión base de equipos.">
                <form method="POST" action="{{ route('team.admin.teams.store') }}" class="space-y-3">
                    @csrf
                    <input name="name" type="text" required placeholder="Nombre del equipo"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <textarea name="description" rows="2" placeholder="Descripción (opcional)"
                              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>
                    <x-ui.button type="submit">Guardar equipo</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Asignar miembro" desc="Vincular empleado a un equipo.">
                <form method="POST" action="{{ route('team.admin.members.store') }}" class="space-y-3">
                    @csrf
                    <select name="team_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Equipo</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Empleado</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>
                    <input name="start_date" type="date" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <x-ui.button type="submit">Asignar miembro</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Asignar coordinador" desc="UC-COOR-01: coordinador único por equipo.">
                <form method="POST" action="{{ route('team.admin.coordinators.assign') }}" class="space-y-3">
                    @csrf
                    <select name="team_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Equipo</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <select name="coordinator_employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Coordinador</option>
                        @foreach ($coordinators as $coordinator)
                            <option value="{{ $coordinator->id }}">{{ $coordinator->first_name }} {{ $coordinator->last_name }}</option>
                        @endforeach
                    </select>
                    <x-ui.button type="submit">Asignar coordinador</x-ui.button>
                </form>
            </x-common.component-card>
        </div>

        <x-common.component-card title="Equipos y miembros" desc="Resumen de equipos, coordinador y personal activo.">
            <div class="space-y-4">
                @forelse ($teams as $team)
                    <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $team->name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $team->description ?? 'Sin descripción.' }}</p>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Coordinador: {{ $team->coordinator ? $team->coordinator->first_name.' '.$team->coordinator->last_name : 'No asignado' }}
                            </div>
                        </div>

                        <ul class="mt-3 list-disc pl-5 text-sm text-gray-600 dark:text-gray-300">
                            @forelse ($team->activeMembers as $member)
                                <li>{{ $member->employee?->first_name }} {{ $member->employee?->last_name }}</li>
                            @empty
                                <li>Sin miembros activos.</li>
                            @endforelse
                        </ul>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay equipos registrados.</p>
                @endforelse
            </div>
        </x-common.component-card>
    </div>
@endsection
