<?php

declare(strict_types=1);

use App\Modules\Workflow\Http\Controllers\LeaveWorkflowController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'force.password.change', 'employee.association'])
    ->prefix('workflow')
    ->name('workflow.')
    ->group(function (): void {
        Route::get('/leaves', [LeaveWorkflowController::class, 'index'])->name('leave.index');
        Route::post('/leaves', [LeaveWorkflowController::class, 'store'])->name('leave.store');
        Route::post('/leaves/{leaveRequest}/approve', [LeaveWorkflowController::class, 'approve'])->name('leave.approve');
        Route::post('/leaves/{leaveRequest}/reject', [LeaveWorkflowController::class, 'reject'])->name('leave.reject');

        Route::post('/shift-swaps', [LeaveWorkflowController::class, 'storeShiftSwap'])->name('shift_swap.store');
        Route::post('/shift-swaps/{shiftSwapRequest}/respond', [LeaveWorkflowController::class, 'respondShiftSwap'])->name('shift_swap.respond');
        Route::post('/shift-swaps/{shiftSwapRequest}/review', [LeaveWorkflowController::class, 'reviewShiftSwap'])->name('shift_swap.review');

        Route::post('/exceptions/direct', [LeaveWorkflowController::class, 'storeDirectException'])->name('exceptions.direct.store');
        Route::post('/exceptions/bulk', [LeaveWorkflowController::class, 'storeBulkExceptions'])->name('exceptions.bulk.store');
    });
