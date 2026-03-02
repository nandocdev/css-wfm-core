<?php

declare(strict_types=1);

namespace App\Modules\Team\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTeamMemberRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
