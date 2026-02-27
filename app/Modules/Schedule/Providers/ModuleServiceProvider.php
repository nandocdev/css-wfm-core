<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Providers;

use App\Contracts\Schedule\ScheduleModuleContract;
use App\Modules\Schedule\Actions\ScheduleModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ScheduleModuleContract::class, ScheduleModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'schedule');
    }
}
