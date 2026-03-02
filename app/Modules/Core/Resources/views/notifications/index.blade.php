@extends('layouts.app')

@section('content')
    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator $notifications */
        /** @var int $unreadCount */
    @endphp

    <x-common.page-breadcrumb pageTitle="Notificaciones" />

    <div class="space-y-6">
        @if (session('status'))
            <x-ui.alert variant="success" :message="session('status')" />
        @endif

        @error('notification')
            <x-ui.alert variant="error" :message="$message" />
        @enderror

        <x-common.component-card title="Bandeja de notificaciones" desc="Alertas del sistema recibidas por correo y disponibles en historial.">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No leídas: {{ $unreadCount }}
                </p>
                <form method="POST" action="{{ route('core.notifications.read-all') }}">
                    @csrf
                    <x-ui.button type="submit" variant="outline">Marcar todas como leídas</x-ui.button>
                </form>
            </div>

            <div class="space-y-4">
                @forelse ($notifications as $notification)
                    @php
                        $data = is_array($notification->data) ? $notification->data : [];
                        $title = is_string($data['title'] ?? null) ? $data['title'] : 'Notificación';
                        $message = is_string($data['message'] ?? null) ? $data['message'] : 'Sin detalle adicional.';
                        $url = is_string($data['url'] ?? null) ? $data['url'] : null;
                    @endphp

                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $title }}</h4>
                            @if ($notification->read_at)
                                <x-ui.badge color="success" size="sm">Leída</x-ui.badge>
                            @else
                                <x-ui.badge color="warning" size="sm">Nueva</x-ui.badge>
                            @endif
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $message }}</p>

                        <div class="mt-3 flex items-center justify-between gap-3">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ optional($notification->created_at)->format('d/m/Y H:i') }}
                            </p>

                            <div class="flex items-center gap-2">
                                @if ($url)
                                    <a href="{{ $url }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">
                                        Ver detalle
                                    </a>
                                @endif

                                @if (! $notification->read_at)
                                    <form method="POST" action="{{ route('core.notifications.read', ['notification' => $notification->id]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.button type="submit" variant="outline" size="sm">Marcar leída</x-ui.button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay notificaciones para mostrar.</p>
                @endforelse
            </div>

            <div>
                {{ $notifications->links() }}
            </div>
        </x-common.component-card>
    </div>
@endsection
