<?php

declare(strict_types=1);

namespace App\Modules\Employee\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreEmployeeRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasRole('Administrador');
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'employee_number' => is_string($this->input('employee_number')) ? strip_tags(trim($this->input('employee_number'))) : null,
            'username' => is_string($this->input('username')) ? strip_tags(trim($this->input('username'))) : null,
            'first_name' => is_string($this->input('first_name')) ? strip_tags(trim($this->input('first_name'))) : null,
            'last_name' => is_string($this->input('last_name')) ? strip_tags(trim($this->input('last_name'))) : null,
            'email' => is_string($this->input('email')) ? mb_strtolower(trim($this->input('email'))) : null,
            'address' => is_string($this->input('address')) ? strip_tags(trim($this->input('address'))) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'employee_number' => ['nullable', 'string', 'max:100', 'unique:employees,employee_number'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:employees,username'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:employees,email'],
            'birth_date' => ['required', 'date'],
            'gender' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'township_id' => ['required', 'integer', 'exists:townships,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'parent_id' => ['nullable', 'integer', 'exists:employees,id'],
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'employment_status_id' => ['required', 'integer', 'exists:employment_statuses,id'],
            'hire_date' => ['required', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_manager' => ['nullable', 'boolean'],
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
        ];
    }
}
