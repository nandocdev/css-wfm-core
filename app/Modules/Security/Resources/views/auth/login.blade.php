@extends('layouts.fullscreen-layout')

@section('content')
    <div class="mx-auto flex min-h-screen w-full max-w-md items-center px-4 py-10">
        <x-common.component-card title="Iniciar sesión" desc="Accede al módulo de seguridad con tus credenciales.">
            @if (session('status'))
                <x-ui.alert variant="success" :message="session('status')" />
            @endif

            @if ($errors->any())
                <x-ui.alert variant="error" title="No se pudo iniciar sesión">
                    <ul class="mt-2 list-disc pl-5 text-sm text-gray-500 dark:text-gray-400">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('security.auth.login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Correo electrónico
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Contraseña
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

                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('security.auth.forgot-password.form') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600">
                        ¿Olvidaste tu contraseña?
                    </a>
                    <x-ui.button type="submit">Ingresar</x-ui.button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection
