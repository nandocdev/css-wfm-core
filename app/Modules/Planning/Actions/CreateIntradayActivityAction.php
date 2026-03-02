<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Planning\Models\IntradayActivity;
use App\Modules\Planning\Models\WeeklySchedule;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class CreateIntradayActivityAction {
    /**
     * @param  array{name:string, weekly_schedule_id:int, activity_date:string, start_time:string, end_time:string, max_participants:int|null, notes:string|null}  $payload
     * @throws ValidationException
     */
    public function execute(array $payload, int $createdBy): IntradayActivity {
        /** @var WeeklySchedule $weeklySchedule */
        $weeklySchedule = WeeklySchedule::query()->findOrFail($payload['weekly_schedule_id']);

        $activityDate = CarbonImmutable::parse($payload['activity_date']);
        $weekStart = CarbonImmutable::parse((string) $weeklySchedule->week_start_date);
        $weekEnd = CarbonImmutable::parse((string) $weeklySchedule->week_end_date);

        if ($activityDate->lt($weekStart) || $activityDate->gt($weekEnd)) {
            throw ValidationException::withMessages([
                'activity_date' => 'La fecha de la actividad debe estar dentro del rango de la semana seleccionada.',
            ]);
        }

        $hasConflict = IntradayActivity::query()
            ->where('weekly_schedule_id', $weeklySchedule->id)
            ->whereDate('activity_date', $activityDate->toDateString())
            ->where('start_time', '<', $payload['end_time'])
            ->where('end_time', '>', $payload['start_time'])
            ->exists();

        if ($hasConflict) {
            throw ValidationException::withMessages([
                'start_time' => 'Existe una actividad intradía que se superpone con el rango horario indicado.',
            ]);
        }

        /** @var IntradayActivity $activity */
        $activity = DB::transaction(function () use ($payload, $weeklySchedule, $createdBy): IntradayActivity {
            return IntradayActivity::query()->create([
                'weekly_schedule_id' => $weeklySchedule->id,
                'name' => $payload['name'],
                'activity_date' => $payload['activity_date'],
                'start_time' => $payload['start_time'],
                'end_time' => $payload['end_time'],
                'max_participants' => $payload['max_participants'],
                'notes' => $payload['notes'],
                'created_by' => $createdBy,
            ]);
        });

        return $activity;
    }
}
