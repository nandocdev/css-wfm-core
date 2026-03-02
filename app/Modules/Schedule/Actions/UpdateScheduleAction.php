<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Actions;

use App\Modules\Schedule\Models\Schedule;
use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class UpdateScheduleAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(Schedule $schedule, array $payload): Schedule {
        if ($schedule->weeklyAssignments()->exists() && !(bool) ($payload['confirm_change'] ?? false)) {
            throw ValidationException::withMessages([
                'confirm_change' => 'El horario tiene asignaciones activas. Confirma el cambio para continuar.',
            ]);
        }

        $computedTotalMinutes = $this->computeTotalMinutes(
            (string) $payload['start_time'],
            (string) $payload['end_time'],
            (int) $payload['lunch_minutes'],
            (int) $payload['break_minutes'],
        );

        /** @var Schedule $updated */
        $updated = $this->databaseManager->transaction(function () use ($schedule, $payload, $computedTotalMinutes): Schedule {
            $schedule->forceFill([
                'name' => $payload['name'],
                'start_time' => $payload['start_time'],
                'end_time' => $payload['end_time'],
                'lunch_minutes' => $payload['lunch_minutes'],
                'break_minutes' => $payload['break_minutes'],
                'total_minutes' => $computedTotalMinutes,
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ])->save();

            return $schedule;
        });

        return $updated;
    }

    private function computeTotalMinutes(string $startTime, string $endTime, int $lunchMinutes, int $breakMinutes): int {
        $start = CarbonImmutable::createFromFormat('H:i', $startTime);
        $end = CarbonImmutable::createFromFormat('H:i', $endTime);

        $gross = $start->diffInMinutes($end, false);

        return max(0, $gross - $lunchMinutes - $breakMinutes);
    }
}
