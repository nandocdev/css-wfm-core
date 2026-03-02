<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Actions;

use App\Modules\Schedule\Models\Schedule;
use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;

final readonly class CreateScheduleAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(array $payload): Schedule {
        $computedTotalMinutes = $this->computeTotalMinutes(
            (string) $payload['start_time'],
            (string) $payload['end_time'],
            (int) $payload['lunch_minutes'],
            (int) $payload['break_minutes'],
        );

        /** @var Schedule $schedule */
        $schedule = $this->databaseManager->transaction(function () use ($payload, $computedTotalMinutes): Schedule {
            /** @var Schedule $created */
            $created = Schedule::query()->create([
                'name' => $payload['name'],
                'start_time' => $payload['start_time'],
                'end_time' => $payload['end_time'],
                'lunch_minutes' => $payload['lunch_minutes'],
                'break_minutes' => $payload['break_minutes'],
                'total_minutes' => $computedTotalMinutes,
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]);

            return $created;
        });

        return $schedule;
    }

    private function computeTotalMinutes(string $startTime, string $endTime, int $lunchMinutes, int $breakMinutes): int {
        $start = CarbonImmutable::createFromFormat('H:i', $startTime);
        $end = CarbonImmutable::createFromFormat('H:i', $endTime);

        $gross = $start->diffInMinutes($end, false);

        return max(0, $gross - $lunchMinutes - $breakMinutes);
    }
}
