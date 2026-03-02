<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignIntradayActivityRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'intraday_activity_id' => ['required', 'integer', 'exists:intraday_activities,id'],
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'distinct', 'exists:employees,id'],
        ];
    }
}
