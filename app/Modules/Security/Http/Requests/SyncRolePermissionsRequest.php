<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SyncRolePermissionsRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['required', 'integer', 'exists:permissions,id'],
        ];
    }
}
