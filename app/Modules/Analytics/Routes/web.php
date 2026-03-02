<?php

declare(strict_types=1);

use App\Modules\Analytics\Http\Controllers\AnalyticsMonitoringController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'force.password.change', 'employee.association'])
    ->prefix('analytics')
    ->name('analytics.')
    ->group(function (): void {
        Route::get('/monitoring', [AnalyticsMonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/export', [AnalyticsMonitoringController::class, 'export'])->name('monitoring.export');
    });
