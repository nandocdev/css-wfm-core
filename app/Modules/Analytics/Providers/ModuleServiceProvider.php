<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Providers;

use App\Contracts\Analytics\AnalyticsModuleContract;
use App\Modules\Analytics\Actions\AnalyticsModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AnalyticsModuleContract::class, AnalyticsModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'analytics');
    }
}
