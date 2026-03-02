<?php

declare(strict_types=1);

namespace App\Modules\Organization\Actions;

use App\Modules\Organization\Models\Directorate;
use Illuminate\Database\DatabaseManager;

final readonly class CreateDirectorateAction {
    public function __construct(
        private DatabaseManager $databaseManager,
    ) {
    }

    /**
     * @param array{name:string,description?:string|null} $payload
     */
    public function execute(array $payload): Directorate {
        /** @var Directorate $directorate */
        $directorate = $this->databaseManager->transaction(function () use ($payload): Directorate {
            return Directorate::query()->create([
                'name' => $payload['name'],
                'description' => $payload['description'] ?? null,
            ]);
        });

        return $directorate;
    }
}
