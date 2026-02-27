<?php

declare(strict_types=1);

namespace App\Modules\Security\Providers;

use App\Contracts\Security\SecurityModuleContract;
use App\Modules\Security\Actions\SecurityModuleAction;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(SecurityModuleContract::class, SecurityModuleAction::class);
        $this->app->bind(StatefulGuard::class, static function ($app): StatefulGuard {
            return $app['auth']->guard(config('auth.defaults.guard', 'web'));
        });
    }

    public function boot(): void {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'security');
    }
}
