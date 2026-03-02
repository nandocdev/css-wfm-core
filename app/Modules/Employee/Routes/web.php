<?php

declare(strict_types=1);

use App\Modules\Employee\Http\Controllers\EmployeeManagementController;
use App\Modules\Employee\Http\Controllers\MyEmployeeProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function (): void {
        Route::middleware(['auth', 'force.password.change', 'employee.association'])->group(function (): void {
            Route::get('/my-profile', [MyEmployeeProfileController::class, 'show'])->name('profile.show');
        });

        Route::middleware(['auth', 'force.password.change'])->prefix('admin')->name('admin.')->group(function (): void {
            Route::get('/manage', [EmployeeManagementController::class, 'manage'])->name('manage');
            Route::post('/employees', [EmployeeManagementController::class, 'storeEmployee'])->name('employees.store');
            Route::post('/employees/dependents', [EmployeeManagementController::class, 'storeDependent'])->name('employees.dependents.store');
            Route::post('/employees/disabilities', [EmployeeManagementController::class, 'storeDisability'])->name('employees.disabilities.store');
            Route::post('/employees/diseases', [EmployeeManagementController::class, 'storeDisease'])->name('employees.diseases.store');
            Route::post('/employees/import', [EmployeeManagementController::class, 'import'])->name('employees.import');
        });
    });
