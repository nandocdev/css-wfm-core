<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateWfmSettingsRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'late_tolerance_minutes' => ['required', 'integer', 'min:0', 'max:180'],
            'early_leave_tolerance_minutes' => ['required', 'integer', 'min:0', 'max:180'],
            'approval_threshold_hours' => ['required', 'integer', 'min:1', 'max:168'],
            'max_overtime_minutes' => ['required', 'integer', 'min:0', 'max:600'],
            'allow_force_approval' => ['nullable', 'boolean'],
        ];
    }
}
