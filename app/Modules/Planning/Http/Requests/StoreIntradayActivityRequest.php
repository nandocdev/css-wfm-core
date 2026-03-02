<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreIntradayActivityRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM']);
    }

    protected function prepareForValidation(): void {
        $name = $this->input('name');
        $notes = $this->input('notes');

        $this->merge([
            'name' => is_string($name) ? trim(strip_tags($name)) : $name,
            'notes' => is_string($notes) ? trim(strip_tags($notes)) : $notes,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'weekly_schedule_id' => ['required', 'integer', 'exists:weekly_schedules,id'],
            'name' => ['required', 'string', 'max:120'],
            'activity_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
