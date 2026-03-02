<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreBulkExceptionRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM']);
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
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'distinct', 'exists:employees,id'],
            'incident_type_id' => ['required', 'integer', 'exists:incident_types,id'],
            'type' => ['required', 'string', 'in:full,partial'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
            'justification' => ['required', 'string', 'max:1000'],
        ];
    }
}
