<?php

declare(strict_types=1);

namespace App\Contracts\Schedule;

interface ScheduleModuleContract
{
    public function execute(): void;
}
