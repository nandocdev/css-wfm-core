<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->input('name')) ? strip_tags(trim($this->input('name'))) : null,
            'email' => is_string($this->input('email')) ? mb_strtolower(trim($this->input('email'))) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
