<?php

declare(strict_types=1);

namespace App\Contracts\Workflow;

interface WorkflowModuleContract
{
    public function execute(): void;
}
