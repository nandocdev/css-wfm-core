<?php

declare(strict_types=1);

namespace App\Contracts\Core;

interface CoreModuleContract
{
    public function execute(): void;
}
