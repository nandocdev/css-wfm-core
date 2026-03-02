<?php

declare(strict_types=1);

use App\Modules\Planning\Http\Controllers\MyPlanningController;
use App\Modules\Planning\Http\Controllers\CoordinatorBreakController;
use App\Modules\Planning\Http\Controllers\IntradayPlanningController;
use App\Modules\Planning\Http\Controllers\WeeklyPlanningController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'force.password.change', 'employee.association'])
    ->prefix('planning')
    ->name('planning.')
    ->group(function (): void {
        Route::get('/my-schedule', [MyPlanningController::class, 'current'])->name('operator.current');
        Route::get('/my-schedule/history', [MyPlanningController::class, 'history'])->name('operator.history');
        Route::get('/my-schedule/exceptions', [MyPlanningController::class, 'exceptions'])->name('operator.exceptions');
        Route::get('/my-day', [MyPlanningController::class, 'myDay'])->name('operator.my_day');

        Route::get('/weekly', [WeeklyPlanningController::class, 'index'])->name('weekly.index');
        Route::post('/weekly', [WeeklyPlanningController::class, 'store'])->name('weekly.store');
        Route::post('/weekly/assign-mass', [WeeklyPlanningController::class, 'massAssign'])->name('weekly.assign.mass');
        Route::post('/weekly/{weeklySchedule}/publish', [WeeklyPlanningController::class, 'publish'])->name('weekly.publish');

        Route::get('/intraday', [IntradayPlanningController::class, 'index'])->name('intraday.index');
        Route::post('/intraday', [IntradayPlanningController::class, 'store'])->name('intraday.store');
        Route::post('/intraday/assign', [IntradayPlanningController::class, 'assign'])->name('intraday.assign');

        Route::get('/coordinator/break-overrides', [CoordinatorBreakController::class, 'index'])->name('coordinator.breaks.index');
        Route::post('/coordinator/break-overrides', [CoordinatorBreakController::class, 'store'])->name('coordinator.breaks.store');
    });
