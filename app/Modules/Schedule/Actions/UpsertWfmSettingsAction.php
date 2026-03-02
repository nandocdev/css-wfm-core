<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Actions;

use App\Modules\Schedule\Models\WfmSetting;
use Illuminate\Database\DatabaseManager;

final readonly class UpsertWfmSettingsAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(array $payload): WfmSetting {
        /** @var WfmSetting $setting */
        $setting = $this->databaseManager->transaction(function () use ($payload): WfmSetting {
            $current = WfmSetting::query()->first();

            if ($current === null) {
                /** @var WfmSetting $created */
                $created = WfmSetting::query()->create([
                    'late_tolerance_minutes' => $payload['late_tolerance_minutes'],
                    'early_leave_tolerance_minutes' => $payload['early_leave_tolerance_minutes'],
                    'approval_threshold_hours' => $payload['approval_threshold_hours'],
                    'max_overtime_minutes' => $payload['max_overtime_minutes'],
                    'allow_force_approval' => (bool) $payload['allow_force_approval'],
                ]);

                return $created;
            }

            $current->forceFill([
                'late_tolerance_minutes' => $payload['late_tolerance_minutes'],
                'early_leave_tolerance_minutes' => $payload['early_leave_tolerance_minutes'],
                'approval_threshold_hours' => $payload['approval_threshold_hours'],
                'max_overtime_minutes' => $payload['max_overtime_minutes'],
                'allow_force_approval' => (bool) $payload['allow_force_approval'],
            ])->save();

            return $current;
        });

        return $setting;
    }
}
