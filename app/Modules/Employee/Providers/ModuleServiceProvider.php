<?php

declare(strict_types=1);

namespace App\Modules\Employee\Providers;

use App\Contracts\Employee\EmployeeModuleContract;
use App\Modules\Employee\Actions\EmployeeModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeModuleContract::class, EmployeeModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'employee');
    }
}
