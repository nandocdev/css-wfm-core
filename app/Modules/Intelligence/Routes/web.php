<?php

declare(strict_types=1);

use App\Modules\Intelligence\Http\Controllers\IntelligenceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'force.password.change', 'employee.association'])
    ->prefix('intelligence')
    ->name('intelligence.')
    ->group(function (): void {
        Route::get('/operations', [IntelligenceController::class, 'index'])->name('operations.index');
        Route::post('/resolve-effective-schedule', [IntelligenceController::class, 'resolveEffectiveSchedule'])->name('schedule.resolve');
        Route::post('/exceptions/{leaveRequest}/force-approve', [IntelligenceController::class, 'forceApproveInstitutionalException'])->name('exceptions.force_approve');
        Route::post('/maintenance/reprocess', [IntelligenceController::class, 'reprocess'])->name('maintenance.reprocess');
    });
