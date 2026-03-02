<?php

declare(strict_types=1);

namespace App\Modules\Organization\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StorePositionRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'title' => is_string($this->input('title')) ? strip_tags(trim($this->input('title'))) : null,
            'position_code' => is_string($this->input('position_code')) ? mb_strtoupper(strip_tags(trim($this->input('position_code')))) : null,
            'description' => is_string($this->input('description')) ? strip_tags(trim($this->input('description'))) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'title' => ['required', 'string', 'max:255'],
            'position_code' => ['required', 'string', 'max:255', 'unique:positions,position_code'],
            'description' => ['nullable', 'string', 'max:1000'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }
}
