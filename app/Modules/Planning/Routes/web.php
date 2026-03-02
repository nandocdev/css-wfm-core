<?php

declare(strict_types=1);

use App\Modules\Planning\Http\Controllers\MyPlanningController;
use App\Modules\Planning\Http\Controllers\WeeklyPlanningController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'force.password.change', 'employee.association'])
    ->prefix('planning')
    ->name('planning.')
    ->group(function (): void {
        Route::get('/my-schedule', [MyPlanningController::class, 'current'])->name('operator.current');
        Route::get('/my-schedule/history', [MyPlanningController::class, 'history'])->name('operator.history');
        Route::get('/my-schedule/exceptions', [MyPlanningController::class, 'exceptions'])->name('operator.exceptions');

        Route::get('/weekly', [WeeklyPlanningController::class, 'index'])->name('weekly.index');
        Route::post('/weekly', [WeeklyPlanningController::class, 'store'])->name('weekly.store');
        Route::post('/weekly/assign-mass', [WeeklyPlanningController::class, 'massAssign'])->name('weekly.assign.mass');
        Route::post('/weekly/{weeklySchedule}/publish', [WeeklyPlanningController::class, 'publish'])->name('weekly.publish');
    });
