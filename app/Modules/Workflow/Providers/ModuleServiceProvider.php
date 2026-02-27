<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Providers;

use App\Contracts\Workflow\WorkflowModuleContract;
use App\Modules\Workflow\Actions\WorkflowModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WorkflowModuleContract::class, WorkflowModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'workflow');
    }
}
