@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Mi perfil" />

    <div class="mx-auto max-w-3xl">
        <x-common.component-card title="Perfil de usuario" desc="Información de tu cuenta en modo solo lectura.">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Nombre</p>
                    <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $user->name }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Correo</p>
                    <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Estado</p>
                    <p class="mt-1 text-sm text-gray-800 dark:text-white/90">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Último acceso</p>
                    <p class="mt-1 text-sm text-gray-800 dark:text-white/90">
                        {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Sin registro' }}
                    </p>
                </div>
            </div>

            <div class="pt-2">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Roles asignados</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse ($roleNames as $roleName)
                        <x-ui.badge color="info" size="sm">{{ $roleName }}</x-ui.badge>
                    @empty
                        <x-ui.badge color="warning" size="sm">Sin roles asignados</x-ui.badge>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <a href="{{ route('security.auth.change-password.form') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">
                    Cambiar contraseña
                </a>
            </div>
        </x-common.component-card>
    </div>
@endsection
