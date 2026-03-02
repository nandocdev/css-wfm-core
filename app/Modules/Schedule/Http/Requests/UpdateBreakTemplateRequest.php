<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Http\Requests;

use App\Modules\Schedule\Models\BreakTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateBreakTemplateRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM']);
    }

    protected function prepareForValidation(): void {
        $this->merge([
            'name' => is_string($this->input('name')) ? strip_tags(trim($this->input('name'))) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        /** @var BreakTemplate $breakTemplate */
        $breakTemplate = $this->route('break_template');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('break_templates', 'name')->where(fn($query) => $query->where('team_id', $this->input('team_id')))->ignore($breakTemplate->id)],
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'lunch_start' => ['required', 'date_format:H:i'],
            'lunch_end' => ['required', 'date_format:H:i', 'different:lunch_start'],
            'break_start' => ['required', 'date_format:H:i'],
            'break_end' => ['required', 'date_format:H:i', 'different:break_start'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
