<?php

declare(strict_types=1);

namespace App\Modules\Core\Providers;

use App\Contracts\Core\CoreModuleContract;
use App\Modules\Core\Actions\CoreModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CoreModuleContract::class, CoreModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'core');
    }
}
