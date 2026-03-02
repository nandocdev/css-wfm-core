<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Actions;

use App\Modules\Schedule\Models\Schedule;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class DeleteScheduleAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    public function execute(Schedule $schedule): void {
        if ($schedule->weeklyAssignments()->exists()) {
            throw ValidationException::withMessages([
                'schedule' => 'No se puede eliminar el horario porque tiene asignaciones semanales activas.',
            ]);
        }

        $this->databaseManager->transaction(function () use ($schedule): void {
            $schedule->delete();
        });
    }
}
