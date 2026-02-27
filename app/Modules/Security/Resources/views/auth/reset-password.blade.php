@extends('layouts.fullscreen-layout')

@section('content')
    <div class="mx-auto flex min-h-screen w-full max-w-md items-center px-4 py-10">
        <x-common.component-card title="Restablecer contraseña" desc="Define una nueva contraseña para tu cuenta.">
            <form method="POST" action="{{ route('security.auth.password.reset') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Correo electrónico
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $email) }}"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
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
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Confirmar contraseña
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                </div>

                <x-ui.button type="submit" class="w-full">Actualizar contraseña</x-ui.button>
            </form>
        </x-common.component-card>
    </div>
@endsection
