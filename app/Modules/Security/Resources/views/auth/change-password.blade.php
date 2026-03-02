@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Cambiar contraseña" />

    <div class="mx-auto max-w-2xl">
        <x-common.component-card title="Actualizar contraseña" desc="Por seguridad, ingresa tu contraseña actual y define una nueva.">
            @if (session('status'))
                <x-ui.alert variant="success" :message="session('status')" />
            @endif

            @if ($errors->any())
                <x-ui.alert variant="error" title="No se pudo actualizar la contraseña">
                    <ul class="mt-2 list-disc pl-5 text-sm text-gray-500 dark:text-gray-400">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('security.auth.change-password.update') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Contraseña actual
                    </label>
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Nueva contraseña
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Confirmar nueva contraseña
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                </div>

                <div class="flex justify-end">
                    <x-ui.button type="submit">Guardar cambios</x-ui.button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
