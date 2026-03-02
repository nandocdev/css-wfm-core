<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ChangeOwnPasswordRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:12', 'confirmed', 'different:current_password'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 12 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'password.different' => 'La nueva contraseña debe ser diferente a la actual.',
        ];
    }
}
