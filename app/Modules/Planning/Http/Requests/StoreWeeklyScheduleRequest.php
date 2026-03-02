<?php

declare(strict_types=1);

namespace App\Modules\Planning\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;

final class StoreWeeklyScheduleRequest extends FormRequest {
    public function authorize(): bool {
        return (bool) $this->user()?->hasAnyRole(['Administrador', 'Analista WFM']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'week_start_date' => ['required', 'date', 'unique:weekly_schedules,week_start_date'],
        ];
    }

    public function withValidator($validator): void {
        $validator->after(function ($validator): void {
            $value = $this->input('week_start_date');

            if (!is_string($value) || $value === '') {
                return;
            }

            $date = CarbonImmutable::parse($value);

            if ((int) $date->dayOfWeekIso !== 1) {
                $validator->errors()->add('week_start_date', 'La semana debe iniciar en lunes.');
            }
        });
    }
}
