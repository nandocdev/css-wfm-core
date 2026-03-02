<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Http\Requests;

use App\Modules\Schedule\Models\Schedule;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateScheduleRequest extends FormRequest {
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
        /** @var Schedule $schedule */
        $schedule = $this->route('schedule');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('schedules', 'name')->ignore($schedule->id)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'different:start_time'],
            'lunch_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'break_minutes' => ['required', 'integer', 'min:0', 'max:180'],
            'confirm_change' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void {
        $validator->after(function ($validator): void {
            $start = (string) $this->input('start_time');
            $end = (string) $this->input('end_time');

            if ($start === '' || $end === '') {
                return;
            }

            $startTime = CarbonImmutable::createFromFormat('H:i', $start);
            $endTime = CarbonImmutable::createFromFormat('H:i', $end);

            if ($startTime->greaterThanOrEqualTo($endTime)) {
                $validator->errors()->add('end_time', 'La hora fin debe ser mayor que la hora inicio.');
                return;
            }

            $gross = $startTime->diffInMinutes($endTime, false);
            $net = $gross - (int) $this->input('lunch_minutes', 0) - (int) $this->input('break_minutes', 0);

            if ($net <= 0) {
                $validator->errors()->add('break_minutes', 'La jornada neta debe ser mayor a 0 minutos.');
            }
        });
    }
}
