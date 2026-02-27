<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Providers;

use App\Contracts\Attendance\AttendanceModuleContract;
use App\Modules\Attendance\Actions\AttendanceModuleAction;
use Illuminate\Support\ServiceProvider;

final class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AttendanceModuleContract::class, AttendanceModuleAction::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'attendance');
    }
}
