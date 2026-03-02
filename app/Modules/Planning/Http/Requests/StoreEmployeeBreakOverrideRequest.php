<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreEmployeeBreakOverrideRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Coordinador']);
    }

    protected function prepareForValidation(): void {
        $reason = $this->input('reason');

        $this->merge([
            'reason' => is_string($reason) ? trim(strip_tags($reason)) : $reason,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'lunch_start' => ['required', 'date_format:H:i'],
            'lunch_end' => ['required', 'date_format:H:i', 'after:lunch_start'],
            'break_start' => ['required', 'date_format:H:i'],
            'break_end' => ['required', 'date_format:H:i', 'after:break_start'],
            'reason' => ['required', 'string', 'max:500'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
        ];
    }
}
