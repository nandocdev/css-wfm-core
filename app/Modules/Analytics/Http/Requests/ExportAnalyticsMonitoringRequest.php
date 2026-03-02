<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExportAnalyticsMonitoringRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM', 'Director', 'Jefe']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'report_type' => ['required', 'string', 'in:executive,management,coordinator'],
            'format' => ['required', 'string', 'in:csv,excel'],
        ];
    }
}
