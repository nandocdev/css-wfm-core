<?php

declare(strict_types=1);

use App\Modules\Organization\Http\Controllers\OrganizationStructureController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'employee.association'])
    ->prefix('organization')
    ->name('organization.')
    ->group(function (): void {
        Route::get('/structure', [OrganizationStructureController::class, 'index'])->name('structure.index');
        Route::post('/directorates', [OrganizationStructureController::class, 'storeDirectorate'])->name('directorates.store');
        Route::post('/departments', [OrganizationStructureController::class, 'storeDepartment'])->name('departments.store');
        Route::post('/positions', [OrganizationStructureController::class, 'storePosition'])->name('positions.store');
    });
