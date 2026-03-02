<?php

declare(strict_types=1);

namespace App\Contracts\Intelligence;

interface IntelligenceModuleContract {
    public function execute(): void;
}
