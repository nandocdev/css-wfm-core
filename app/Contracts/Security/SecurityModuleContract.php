<?php

declare(strict_types=1);

namespace App\Contracts\Security;

interface SecurityModuleContract
{
    public function execute(): void;
}
