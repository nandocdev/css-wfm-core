<?php

declare(strict_types=1);

namespace App\Modules\Intelligence\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ForceApproveInstitutionalExceptionRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM', 'Director', 'Jefe']);
    }

    protected function prepareForValidation(): void {
        $justification = $this->input('justification');

        $this->merge([
            'justification' => is_string($justification) ? trim(strip_tags($justification)) : $justification,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'justification' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'justification.required' => 'Debes registrar la justificación de la aprobación forzada.',
            'justification.max' => 'La justificación no puede exceder 1000 caracteres.',
        ];
    }
}
