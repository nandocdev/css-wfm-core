<?php

declare(strict_types=1);

namespace App\Contracts\Attendance;

interface AttendanceModuleContract
{
    public function execute(): void;
}
