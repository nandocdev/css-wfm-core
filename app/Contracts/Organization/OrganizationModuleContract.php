<?php

declare(strict_types=1);

namespace App\Contracts\Organization;

interface OrganizationModuleContract
{
    public function execute(): void;
}
