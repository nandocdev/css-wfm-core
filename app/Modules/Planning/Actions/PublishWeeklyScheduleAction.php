<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Planning\Models\WeeklySchedule;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class PublishWeeklyScheduleAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    public function execute(WeeklySchedule $weeklySchedule, int $publishedBy): WeeklySchedule {
        if ($weeklySchedule->status !== 'draft') {
            throw ValidationException::withMessages([
                'weekly_schedule' => 'Solo se puede publicar una planificación en estado draft.',
            ]);
        }

        if (!$weeklySchedule->assignments()->exists()) {
            throw ValidationException::withMessages([
                'weekly_schedule' => 'No se puede publicar una planificación sin asignaciones.',
            ]);
        }

        /** @var WeeklySchedule $published */
        $published = $this->databaseManager->transaction(function () use ($weeklySchedule, $publishedBy): WeeklySchedule {
            $weeklySchedule->forceFill([
                'status' => 'published',
                'published_at' => now(),
                'published_by' => $publishedBy,
            ])->save();

            return $weeklySchedule;
        });

        return $published;
    }
}
