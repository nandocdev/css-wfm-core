@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Workflow de permisos y excepciones" />

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

        @if ($employee === null)
            <x-ui.alert variant="warning" title="Sin empleado asociado">
                No existe un empleado activo vinculado a tu usuario.
            </x-ui.alert>
        @else
            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-common.component-card title="Solicitar permiso" desc="UC-OP-05/06/07: registro de solicitud total o parcial con validación de solapamientos.">
                    <form method="POST" action="{{ route('workflow.leave.store') }}" class="space-y-3">
                        @csrf
                        <select name="incident_type_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Tipo de permiso</option>
                            @foreach ($incidentTypes as $incidentType)
                                <option value="{{ $incidentType->id }}">{{ $incidentType->name }}</option>
                            @endforeach
                        </select>

                        <select name="type" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Modalidad</option>
                            <option value="full">Total</option>
                            <option value="partial">Parcial</option>
                        </select>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <input name="start_datetime" type="datetime-local" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                            <input name="end_datetime" type="datetime-local" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        </div>

                        <textarea name="justification" rows="3" required placeholder="Justificación"
                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>

                        <x-ui.button type="submit">Enviar solicitud</x-ui.button>
                    </form>
                </x-common.component-card>

                <x-common.component-card title="Bandeja administrativa" desc="UC-COOR-05: pendientes de aprobación según reglas jerárquicas.">
                    <div class="space-y-3">
                        @forelse ($pendingApprovals as $pending)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $pending->employee?->first_name }} {{ $pending->employee?->last_name }} · {{ $pending->incidentType?->name ?? 'Permiso' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $pending->start_datetime?->format('d/m/Y H:i') }} - {{ $pending->end_datetime?->format('d/m/Y H:i') }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $pending->justification }}</p>

                                <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <form method="POST" action="{{ route('workflow.leave.approve', $pending) }}" class="space-y-2">
                                        @csrf
                                        <textarea name="comments" rows="2" placeholder="Comentario (opcional)"
                                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-900"></textarea>
                                        <x-ui.button type="submit">Aprobar</x-ui.button>
                                    </form>
                                    <form method="POST" action="{{ route('workflow.leave.reject', $pending) }}" class="space-y-2">
                                        @csrf
                                        <textarea name="comments" rows="2" required placeholder="Motivo del rechazo"
                                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-900"></textarea>
                                        <x-ui.button type="submit">Rechazar</x-ui.button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No tienes pendientes en la bandeja administrativa.</p>
                        @endforelse
                    </div>
                </x-common.component-card>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-common.component-card title="Solicitar cambio de turno" desc="UC-OP-08: solicitud con validación de horarios y excepciones activas.">
                    <form method="POST" action="{{ route('workflow.shift_swap.store') }}" class="space-y-3">
                        @csrf
                        <select name="target_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="">Empleado destino (mismo rol)</option>
                            @foreach ($swapCandidates as $candidate)
                                <option value="{{ $candidate->id }}">{{ $candidate->first_name }} {{ $candidate->last_name }}</option>
                            @endforeach
                        </select>

                        <input name="swap_date" type="date" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />

                        <x-ui.button type="submit">Solicitar cambio</x-ui.button>
                    </form>
                </x-common.component-card>

                <x-common.component-card title="Solicitudes recibidas" desc="UC-OP-09: aceptar o rechazar solicitudes dirigidas a tu usuario.">
                    <div class="space-y-3">
                        @forelse ($incomingShiftSwapRequests as $incomingSwap)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $incomingSwap->requester?->first_name }} {{ $incomingSwap->requester?->last_name }} · {{ $incomingSwap->swap_date?->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Tu turno: {{ $incomingSwap->targetAssignment?->schedule?->name ?? 'N/D' }} → Nuevo turno: {{ $incomingSwap->requesterAssignment?->schedule?->name ?? 'N/D' }}
                                </p>

                                <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <form method="POST" action="{{ route('workflow.shift_swap.respond', $incomingSwap) }}" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="action" value="accepted" />
                                        <x-ui.button type="submit">Aceptar</x-ui.button>
                                    </form>
                                    <form method="POST" action="{{ route('workflow.shift_swap.respond', $incomingSwap) }}" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="action" value="rejected" />
                                        <textarea name="comments" rows="2" placeholder="Motivo (opcional)"
                                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-900"></textarea>
                                        <x-ui.button type="submit">Rechazar</x-ui.button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No tienes solicitudes de cambio pendientes de respuesta.</p>
                        @endforelse
                    </div>
                </x-common.component-card>
            </div>

            @if ($isCoordinator || $isWfm)
                <x-common.component-card title="Bandeja de aprobación de cambios de turno" desc="UC-COOR-04: aprobar o rechazar solicitudes aceptadas en un único nivel.">
                    <div class="space-y-3">
                        @forelse ($pendingShiftSwapApprovals as $pendingSwap)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $pendingSwap->requester?->first_name }} {{ $pendingSwap->requester?->last_name }} ⇄ {{ $pendingSwap->target?->first_name }} {{ $pendingSwap->target?->last_name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Fecha: {{ $pendingSwap->swap_date?->format('d/m/Y') }} · {{ $pendingSwap->requesterAssignment?->schedule?->name ?? 'N/D' }} ⇄ {{ $pendingSwap->targetAssignment?->schedule?->name ?? 'N/D' }}
                                </p>

                                <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <form method="POST" action="{{ route('workflow.shift_swap.review', $pendingSwap) }}" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="action" value="approved" />
                                        <textarea name="comments" rows="2" placeholder="Comentario (opcional)"
                                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-900"></textarea>
                                        <x-ui.button type="submit">Aprobar</x-ui.button>
                                    </form>
                                    <form method="POST" action="{{ route('workflow.shift_swap.review', $pendingSwap) }}" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="action" value="rejected" />
                                        <textarea name="comments" rows="2" required placeholder="Motivo del rechazo"
                                                  class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-900"></textarea>
                                        <x-ui.button type="submit">Rechazar</x-ui.button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">No hay cambios de turno pendientes de aprobación.</p>
                        @endforelse
                    </div>
                </x-common.component-card>
            @endif

            @if ($isCoordinator || $isWfm)
                <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                    <x-common.component-card title="Excepción directa" desc="UC-COOR-06 / UC-WFM-05: creación individual aprobada con trazabilidad.">
                        <form method="POST" action="{{ route('workflow.exceptions.direct.store') }}" class="space-y-3">
                            @csrf
                            <select name="employee_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Empleado</option>
                                @foreach ($exceptionEmployees as $targetEmployee)
                                    <option value="{{ $targetEmployee->id }}">{{ $targetEmployee->first_name }} {{ $targetEmployee->last_name }}</option>
                                @endforeach
                            </select>

                            <select name="incident_type_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Tipo de excepción</option>
                                @foreach ($incidentTypes as $incidentType)
                                    <option value="{{ $incidentType->id }}">{{ $incidentType->name }}</option>
                                @endforeach
                            </select>

                            <select name="type" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Modalidad</option>
                                <option value="full">Total</option>
                                <option value="partial">Parcial</option>
                            </select>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <input name="start_datetime" type="datetime-local" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                <input name="end_datetime" type="datetime-local" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                            </div>

                            <textarea name="justification" rows="3" required placeholder="Justificación de excepción"
                                      class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>

                            <x-ui.button type="submit">Crear excepción individual</x-ui.button>
                        </form>
                    </x-common.component-card>

                    @if ($isWfm)
                        <x-common.component-card title="Excepciones masivas" desc="UC-WFM-05: aplicar excepciones masivas a múltiples colaboradores.">
                            <form method="POST" action="{{ route('workflow.exceptions.bulk.store') }}" class="space-y-3">
                                @csrf
                                <select name="employee_ids[]" multiple required class="min-h-32 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                                    @foreach ($exceptionEmployees as $targetEmployee)
                                        <option value="{{ $targetEmployee->id }}">{{ $targetEmployee->first_name }} {{ $targetEmployee->last_name }}</option>
                                    @endforeach
                                </select>

                                <select name="incident_type_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                                    <option value="">Tipo de excepción</option>
                                    @foreach ($incidentTypes as $incidentType)
                                        <option value="{{ $incidentType->id }}">{{ $incidentType->name }}</option>
                                    @endforeach
                                </select>

                                <select name="type" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                                    <option value="">Modalidad</option>
                                    <option value="full">Total</option>
                                    <option value="partial">Parcial</option>
                                </select>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <input name="start_datetime" type="datetime-local" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                    <input name="end_datetime" type="datetime-local" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                                </div>

                                <textarea name="justification" rows="3" required placeholder="Justificación masiva"
                                          class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-900"></textarea>

                                <x-ui.button type="submit">Crear excepciones masivas</x-ui.button>
                            </form>
                        </x-common.component-card>
                    @endif
                </div>
            @endif

            <x-common.component-card title="Estado de mis solicitudes" desc="UC-OP-07: seguimiento de estado y trazabilidad de aprobación.">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Tipo</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Rango</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Estado</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Última decisión</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($myLeaveRequests as $item)
                                @php $decision = $item->approvals->first(); @endphp
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->incidentType?->name ?? 'Permiso' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->start_datetime?->format('d/m/Y H:i') }} - {{ $item->end_datetime?->format('d/m/Y H:i') }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->status }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                        @if ($decision)
                                            {{ $decision->action }} por {{ $decision->approver?->first_name }} {{ $decision->approver?->last_name }}
                                        @else
                                            Sin decisión
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">No tienes solicitudes registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>

            <x-common.component-card title="Estado de mis cambios de turno" desc="UC-OP-08/09: seguimiento de solicitudes y decisión final del coordinador.">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Destino</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Fecha</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Intercambio</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($myShiftSwapRequests as $item)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->target?->first_name }} {{ $item->target?->last_name }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->swap_date?->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->requesterAssignment?->schedule?->name ?? 'N/D' }} ⇄ {{ $item->targetAssignment?->schedule?->name ?? 'N/D' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->status }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">No tienes cambios de turno registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        @endif
    </div>
@endsection
