<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Planning\Models\WeeklySchedule;
use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;

final readonly class CreateWeeklyScheduleAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    public function execute(string $weekStartDate): WeeklySchedule {
        $start = CarbonImmutable::parse($weekStartDate)->startOfDay();
        $end = $start->addDays(6);

        /** @var WeeklySchedule $weeklySchedule */
        $weeklySchedule = $this->databaseManager->transaction(function () use ($start, $end): WeeklySchedule {
            /** @var WeeklySchedule $created */
            $created = WeeklySchedule::query()->create([
                'week_start_date' => $start->toDateString(),
                'week_end_date' => $end->toDateString(),
                'status' => 'draft',
            ]);

            return $created;
        });

        return $weeklySchedule;
    }
}
