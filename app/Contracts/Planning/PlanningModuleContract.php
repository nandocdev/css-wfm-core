<?php

declare(strict_types=1);

namespace App\Contracts\Planning;

interface PlanningModuleContract
{
    public function execute(): void;
}
