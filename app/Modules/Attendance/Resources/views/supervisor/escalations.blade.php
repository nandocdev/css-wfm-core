@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Escalación operativa" />

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
                No tienes equipo activo para escalar incidencias operativas.
            </x-ui.alert>
        @elseif ($coordinatorUser === null)
            <x-ui.alert variant="warning" title="Sin coordinador asignado">
                Tu equipo no tiene coordinador activo para recibir escalaciones.
            </x-ui.alert>
        @else
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-common.component-card title="Escalar incidencia" desc="UC-SUP-03: notificación al coordinador del equipo.">
                    <form method="POST" action="{{ route('attendance.supervisor.escalations.store') }}" class="space-y-3">
                        @csrf
                        <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Colaborador afectado</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->employee_id }}">{{ $member->employee?->first_name }} {{ $member->employee?->last_name }}</option>
                            @endforeach
                        </select>

                        <select name="severity" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Severidad</option>
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                            <option value="critical">Crítica</option>
                        </select>

                        <textarea name="details" rows="4" required placeholder="Detalle operativo de la incidencia"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>

                        <x-ui.button type="submit">Enviar escalación</x-ui.button>
                    </form>
                </x-common.component-card>

                <x-common.component-card title="Destino de escalación" desc="Contexto del equipo y coordinador receptor.">
                    <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <p><span class="font-medium">Equipo:</span> {{ $team->name }}</p>
                        <p><span class="font-medium">Coordinador:</span> {{ $team->coordinator?->first_name }} {{ $team->coordinator?->last_name }}</p>
                        <p><span class="font-medium">Correo:</span> {{ $coordinatorUser->email }}</p>
                        <p><span class="font-medium">Colaboradores disponibles:</span> {{ $members->count() }}</p>
                    </div>
                </x-common.component-card>
            </div>
        @endif
    </div>
@endsection
