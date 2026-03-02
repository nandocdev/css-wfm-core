<?php

declare(strict_types=1);

namespace App\Modules\Organization\Actions;

use App\Modules\Organization\Models\Position;
use Illuminate\Database\DatabaseManager;

final readonly class CreatePositionAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array{title:string,position_code:string,department_id:int,description?:string|null} $payload
     */
    public function execute(array $payload): Position {
        /** @var Position $position */
        $position = $this->databaseManager->transaction(function () use ($payload): Position {
            return Position::query()->create([
                'title' => $payload['title'],
                'description' => $payload['description'] ?? null,
                'position_code' => $payload['position_code'],
                'department_id' => $payload['department_id'],
            ]);
        });

        return $position;
    }
}
