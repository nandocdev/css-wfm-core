<?php

declare(strict_types=1);

use App\Modules\Attendance\Http\Controllers\CoordinatorAttendanceController;
use App\Modules\Attendance\Http\Controllers\OperatorAttendanceController;
use App\Modules\Attendance\Http\Controllers\SupervisorEscalationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'force.password.change', 'employee.association'])
    ->prefix('attendance')
    ->name('attendance.')
    ->group(function (): void {
        Route::get('/my-attendance', [OperatorAttendanceController::class, 'index'])->name('operator.index');

        Route::get('/coordinator/incidents', [CoordinatorAttendanceController::class, 'index'])->name('coordinator.incidents.index');
        Route::post('/coordinator/incidents', [CoordinatorAttendanceController::class, 'store'])->name('coordinator.incidents.store');

        Route::get('/supervisor/escalations', [SupervisorEscalationController::class, 'index'])->name('supervisor.escalations.index');
        Route::post('/supervisor/escalations', [SupervisorEscalationController::class, 'store'])->name('supervisor.escalations.store');
    });
