<?php

declare(strict_types=1);

namespace App\Modules\Intelligence\Providers;

use App\Contracts\Intelligence\IntelligenceModuleContract;
use App\Modules\Intelligence\Actions\IntelligenceModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider {
    public function register(): void {
        $this->app->bind(IntelligenceModuleContract::class, IntelligenceModuleAction::class);
    }

    public function boot(): void {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'intelligence');
    }
}
