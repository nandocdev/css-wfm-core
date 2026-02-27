<?php

declare(strict_types=1);

namespace App\Modules\Planning\Providers;

use App\Contracts\Planning\PlanningModuleContract;
use App\Modules\Planning\Actions\PlanningModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PlanningModuleContract::class, PlanningModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'planning');
    }
}
