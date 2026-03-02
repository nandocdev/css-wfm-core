<?php

declare(strict_types=1);

namespace App\Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreEmployeeDependentRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'first_name' => is_string($this->input('first_name')) ? strip_tags(trim($this->input('first_name'))) : null,
            'last_name' => is_string($this->input('last_name')) ? strip_tags(trim($this->input('last_name'))) : null,
            'relationship' => is_string($this->input('relationship')) ? strip_tags(trim($this->input('relationship'))) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'is_dependent' => ['nullable', 'boolean'],
        ];
    }
}
