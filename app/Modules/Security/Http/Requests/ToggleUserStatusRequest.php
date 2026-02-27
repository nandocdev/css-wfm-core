<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ToggleUserStatusRequest extends FormRequest
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
            'is_active' => ['required', 'boolean'],
        ];
    }
}
