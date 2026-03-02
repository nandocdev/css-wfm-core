<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreLeaveRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void {
        $justification = $this->input('justification');

        $this->merge([
            'justification' => is_string($justification) ? trim(strip_tags($justification)) : $justification,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'incident_type_id' => ['required', 'integer', 'exists:incident_types,id'],
            'type' => ['required', 'string', 'in:full,partial'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
            'justification' => ['required', 'string', 'max:1000'],
        ];
    }
}
