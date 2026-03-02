<?php

declare(strict_types=1);

namespace App\Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreEmployeeDiseaseRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'description' => is_string($this->input('description')) ? strip_tags(trim($this->input('description'))) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'disease_type_id' => ['required', 'integer', 'exists:disease_types,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'diagnosis_date' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
