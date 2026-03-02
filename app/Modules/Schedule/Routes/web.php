<?php

declare(strict_types=1);

use App\Modules\Schedule\Http\Controllers\ScheduleEngineController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'force.password.change', 'employee.association'])
    ->prefix('schedule')
    ->name('schedule.')
    ->group(function (): void {
        Route::get('/engine', [ScheduleEngineController::class, 'index'])->name('engine.index');

        Route::post('/schedules', [ScheduleEngineController::class, 'storeSchedule'])->name('schedules.store');
        Route::put('/schedules/{schedule}', [ScheduleEngineController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [ScheduleEngineController::class, 'destroySchedule'])->name('schedules.delete');

        Route::post('/break-templates', [ScheduleEngineController::class, 'storeBreakTemplate'])->name('break_templates.store');
        Route::put('/break-templates/{break_template}', [ScheduleEngineController::class, 'updateBreakTemplate'])->name('break_templates.update');
        Route::post('/break-templates/assign', [ScheduleEngineController::class, 'assignBreakTemplate'])->name('break_templates.assign');

        Route::put('/wfm-settings', [ScheduleEngineController::class, 'updateWfmSettings'])->name('wfm.settings.update');
    });
