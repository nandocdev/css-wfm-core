<?php

declare(strict_types=1);

use App\Modules\Team\Http\Controllers\MyTeamController;
use App\Modules\Team\Http\Controllers\TeamManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('team')
    ->name('team.')
    ->group(function (): void {
        Route::middleware(['auth', 'force.password.change', 'employee.association'])
            ->group(function (): void {
                Route::get('/my-team', [MyTeamController::class, 'show'])->name('my-team.show');
            });

        Route::middleware(['auth', 'force.password.change'])
            ->prefix('admin')
            ->name('admin.')
            ->group(function (): void {
                Route::get('/manage', [TeamManagementController::class, 'manage'])->name('manage');
                Route::post('/teams', [TeamManagementController::class, 'storeTeam'])->name('teams.store');
                Route::post('/members', [TeamManagementController::class, 'storeMember'])->name('members.store');
                Route::post('/coordinators/assign', [TeamManagementController::class, 'assignCoordinator'])->name('coordinators.assign');
            });
    });
