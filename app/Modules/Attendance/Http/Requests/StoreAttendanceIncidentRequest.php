<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreAttendanceIncidentRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Coordinador']);
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
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'incident_type_id' => ['required', 'integer', 'exists:incident_types,id'],
            'incident_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'justification' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
