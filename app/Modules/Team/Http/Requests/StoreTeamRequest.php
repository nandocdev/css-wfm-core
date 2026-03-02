<?php

declare(strict_types=1);

namespace App\Modules\Team\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTeamRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'name' => is_string($this->input('name')) ? strip_tags(trim($this->input('name'))) : null,
            'description' => is_string($this->input('description')) ? strip_tags(trim($this->input('description'))) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:teams,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
