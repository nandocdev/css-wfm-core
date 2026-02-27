<?php

declare(strict_types=1);

namespace App\Contracts\Team;

interface TeamModuleContract
{
    public function execute(): void;
}
