<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignUserRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}
