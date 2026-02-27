@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Administración de seguridad" />

    <div class="space-y-6">
        @if (session('status'))
            <x-ui.alert variant="success" :message="session('status')" />
        @endif

        @if ($errors->any())
            <x-ui.alert variant="error" title="Se encontraron errores">
                <ul class="mt-2 list-disc pl-5 text-sm text-gray-500 dark:text-gray-400">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <x-common.component-card title="Crear usuario" desc="Registra una nueva cuenta en el sistema.">
                <form method="POST" action="{{ route('security.admin.users.store') }}" class="space-y-4">
                    @csrf

                    <input name="name" type="text" value="{{ old('name') }}" placeholder="Nombre completo" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="correo@empresa.com" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />

                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 dark:border-gray-700" />
                        Usuario activo
                    </label>

                    <x-ui.button type="submit">Crear usuario</x-ui.button>
                </form>
            </x-common.component-card>

            <x-common.component-card title="Operaciones de administración" desc="Ejecuta acciones por ID para usuarios y roles.">
                <div class="space-y-5">
                    <form method="POST" action="{{ route('security.admin.users.update', ['user' => 0]) }}" class="space-y-3" onsubmit="this.action = this.action.replace('/0', '/' + this.querySelector('[name=user_id]').value)">
                        @csrf
                        @method('PUT')
                        <h4 class="text-sm font-medium text-gray-800 dark:text-white/90">Actualizar usuario</h4>
                        <input name="user_id" type="number" min="1" placeholder="ID de usuario" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="name" type="text" placeholder="Nuevo nombre" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="email" type="email" placeholder="Nuevo correo" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <x-ui.button type="submit" variant="outline">Actualizar</x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('security.admin.users.status.toggle', ['user' => 0]) }}" class="space-y-3" onsubmit="this.action = this.action.replace('/0/status', '/' + this.querySelector('[name=user_id]').value + '/status')">
                        @csrf
                        @method('PATCH')
                        <h4 class="text-sm font-medium text-gray-800 dark:text-white/90">Cambiar estado de usuario</h4>
                        <input name="user_id" type="number" min="1" placeholder="ID de usuario" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <select name="is_active" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                        <x-ui.button type="submit" variant="outline">Aplicar estado</x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('security.admin.users.roles.assign', ['user' => 0]) }}" class="space-y-3" onsubmit="this.action = this.action.replace('/0/roles', '/' + this.querySelector('[name=user_id]').value + '/roles'); this.querySelectorAll('[data-role-source]').forEach((field) => { field.remove(); }); const values = (this.querySelector('[name=role_ids_csv]').value || '').split(',').map((item) => item.trim()).filter((item) => item !== ''); values.forEach((value) => { const input = document.createElement('input'); input.type = 'hidden'; input.name = 'role_ids[]'; input.value = value; input.setAttribute('data-role-source', '1'); this.appendChild(input); });">
                        @csrf
                        @method('PUT')
                        <h4 class="text-sm font-medium text-gray-800 dark:text-white/90">Asignar roles a usuario</h4>
                        <input name="user_id" type="number" min="1" placeholder="ID de usuario" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="role_ids_csv" type="text" placeholder="IDs separados por coma (ej: 1,2)" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <x-ui.button type="submit" variant="outline">Asignar roles</x-ui.button>
                    </form>

                    <form method="POST" action="{{ route('security.admin.roles.permissions.sync', ['role' => 0]) }}" class="space-y-3" onsubmit="this.action = this.action.replace('/0/permissions', '/' + this.querySelector('[name=role_id]').value + '/permissions'); this.querySelectorAll('[data-permission-source]').forEach((field) => { field.remove(); }); const values = (this.querySelector('[name=permission_ids_csv]').value || '').split(',').map((item) => item.trim()).filter((item) => item !== ''); values.forEach((value) => { const input = document.createElement('input'); input.type = 'hidden'; input.name = 'permission_ids[]'; input.value = value; input.setAttribute('data-permission-source', '1'); this.appendChild(input); });">
                        @csrf
                        @method('PUT')
                        <h4 class="text-sm font-medium text-gray-800 dark:text-white/90">Sincronizar permisos de rol</h4>
                        <input name="role_id" type="number" min="1" placeholder="ID de rol" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <input name="permission_ids_csv" type="text" placeholder="IDs separados por coma (ej: 4,5,6)" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900" />
                        <x-ui.button type="submit" variant="outline">Sincronizar permisos</x-ui.button>
                    </form>
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection
