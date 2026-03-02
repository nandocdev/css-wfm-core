<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class MassAssignWeeklyScheduleRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'weekly_schedule_id' => ['required', 'integer', 'exists:weekly_schedules,id'],
            'schedule_id' => ['required', 'integer', 'exists:schedules,id'],
            'break_template_id' => ['nullable', 'integer', 'exists:break_templates,id'],
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ];
    }

    public function withValidator($validator): void {
        $validator->after(function ($validator): void {
            $teamId = $this->input('team_id');
            $employeeIds = $this->input('employee_ids');
            $hasTeam = is_numeric($teamId);
            $hasEmployees = is_array($employeeIds) && count($employeeIds) > 0;

            if (!$hasTeam && !$hasEmployees) {
                $validator->errors()->add('employee_ids', 'Debes seleccionar un equipo o al menos un empleado.');
            }
        });
    }
}
