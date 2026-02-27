<?php

declare(strict_types=1);

namespace App\Modules\Organization\Providers;

use App\Contracts\Organization\OrganizationModuleContract;
use App\Modules\Organization\Actions\OrganizationModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrganizationModuleContract::class, OrganizationModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'organization');
    }
}
