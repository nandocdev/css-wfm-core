<?php

declare(strict_types=1);

namespace App\Modules\Security\Providers;

use App\Contracts\Security\SecurityModuleContract;
use App\Modules\Security\Actions\SecurityModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SecurityModuleContract::class, SecurityModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'security');
    }
}
