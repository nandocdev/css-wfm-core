<?php

declare(strict_types=1);

namespace App\Modules\Team\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AssignCoordinatorRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'coordinator_employee_id' => ['required', 'integer', 'exists:employees,id'],
        ];
    }
}
