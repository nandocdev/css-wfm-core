<?php

declare(strict_types=1);

namespace App\Modules\Intelligence\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ReprocessScheduleInformationRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'date' => is_string($this->input('date')) ? trim($this->input('date')) : $this->input('date'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'date' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'employee_id.required' => 'Debes seleccionar un empleado para reprocesar.',
            'employee_id.exists' => 'El empleado seleccionado no existe.',
            'date.required' => 'Debes indicar la fecha a reprocesar.',
            'date.date' => 'La fecha de reprocesamiento no es válida.',
        ];
    }
}
