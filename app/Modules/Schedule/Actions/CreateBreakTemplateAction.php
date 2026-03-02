<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Actions;

use App\Modules\Schedule\Models\BreakTemplate;
use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

final readonly class CreateBreakTemplateAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(array $payload): BreakTemplate {
        $this->validateRanges((string) $payload['lunch_start'], (string) $payload['lunch_end'], (string) $payload['break_start'], (string) $payload['break_end']);

        /** @var BreakTemplate $template */
        $template = $this->databaseManager->transaction(function () use ($payload): BreakTemplate {
            /** @var BreakTemplate $created */
            $created = BreakTemplate::query()->create([
                'name' => $payload['name'],
                'team_id' => $payload['team_id'],
                'lunch_start' => $payload['lunch_start'],
                'lunch_end' => $payload['lunch_end'],
                'break_start' => $payload['break_start'],
                'break_end' => $payload['break_end'],
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]);

            return $created;
        });

        return $template;
    }

    private function validateRanges(string $lunchStart, string $lunchEnd, string $breakStart, string $breakEnd): void {
        $lunchStartTime = CarbonImmutable::createFromFormat('H:i', $lunchStart);
        $lunchEndTime = CarbonImmutable::createFromFormat('H:i', $lunchEnd);
        $breakStartTime = CarbonImmutable::createFromFormat('H:i', $breakStart);
        $breakEndTime = CarbonImmutable::createFromFormat('H:i', $breakEnd);

        if ($lunchStartTime->greaterThanOrEqualTo($lunchEndTime) || $breakStartTime->greaterThanOrEqualTo($breakEndTime)) {
            throw ValidationException::withMessages([
                'time' => 'Los rangos de almuerzo y descanso deben tener hora inicio menor a hora fin.',
            ]);
        }
    }
}
