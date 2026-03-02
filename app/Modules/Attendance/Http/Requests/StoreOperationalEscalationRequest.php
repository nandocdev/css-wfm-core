<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreOperationalEscalationRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Supervisor', 'Operador II']);
    }

    protected function prepareForValidation(): void {
        $details = $this->input('details');

        $this->merge([
            'details' => is_string($details) ? trim(strip_tags($details)) : $details,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'severity' => ['required', 'string', 'in:low,medium,high,critical'],
            'details' => ['required', 'string', 'max:1000'],
        ];
    }
}
