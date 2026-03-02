<?php

declare(strict_types=1);

namespace App\Modules\Team\Actions;

use App\Modules\Team\Models\Team;
use Illuminate\Database\DatabaseManager;

final readonly class CreateTeamAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function execute(array $payload): Team {
        /** @var Team $team */
        $team = $this->databaseManager->transaction(function () use ($payload): Team {
            /** @var Team $created */
            $created = Team::query()->create([
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
                'is_active' => (bool) ($payload['is_active'] ?? true),
            ]);

            return $created;
        });

        return $team;
    }
}
