<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignBreakTemplateRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'weekly_schedule_assignment_id' => ['required', 'integer', 'exists:weekly_schedule_assignments,id'],
            'break_template_id' => ['required', 'integer', 'exists:break_templates,id'],
        ];
    }
}
