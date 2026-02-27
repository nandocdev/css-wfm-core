<?php

declare(strict_types=1);

namespace App\Modules\Team\Providers;

use App\Contracts\Team\TeamModuleContract;
use App\Modules\Team\Actions\TeamModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TeamModuleContract::class, TeamModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'team');
    }
}
