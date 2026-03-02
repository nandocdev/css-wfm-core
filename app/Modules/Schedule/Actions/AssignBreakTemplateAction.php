<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Actions;

use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Schedule\Models\BreakTemplate;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class AssignBreakTemplateAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    public function execute(int $weeklyScheduleAssignmentId, int $breakTemplateId): WeeklyScheduleAssignment {
        /** @var WeeklyScheduleAssignment|null $assignment */
        $assignment = WeeklyScheduleAssignment::query()->find($weeklyScheduleAssignmentId);

        if ($assignment === null) {
            throw ValidationException::withMessages([
                'weekly_schedule_assignment_id' => 'La asignación semanal indicada no existe.',
            ]);
        }

        /** @var BreakTemplate|null $breakTemplate */
        $breakTemplate = BreakTemplate::query()->find($breakTemplateId);

        if ($breakTemplate === null) {
            throw ValidationException::withMessages([
                'break_template_id' => 'La plantilla de descanso indicada no existe.',
            ]);
        }

        /** @var WeeklyScheduleAssignment $updated */
        $updated = $this->databaseManager->transaction(function () use ($assignment, $breakTemplate): WeeklyScheduleAssignment {
            $assignment->forceFill([
                'break_template_id' => $breakTemplate->id,
                'is_custom_break' => false,
            ])->save();

            return $assignment;
        });

        return $updated;
    }
}
