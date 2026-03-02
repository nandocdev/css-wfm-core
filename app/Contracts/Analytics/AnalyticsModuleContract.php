<?php

declare(strict_types=1);

namespace App\Contracts\Analytics;

interface AnalyticsModuleContract
{
    public function execute(): void;
}
