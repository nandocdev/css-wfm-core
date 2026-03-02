<?php

declare(strict_types=1);

use App\Modules\Security\Http\Controllers\AuthController;
use App\Modules\Security\Http\Controllers\ForcePasswordChangeController;
use App\Modules\Security\Http\Controllers\UserAdministrationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('security')
    ->name('security.')
    ->group(function (): void {
        Route::prefix('auth')->name('auth.')->group(function (): void {
            Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
            Route::post('/login', [AuthController::class, 'login'])->name('login');
            Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('forgot-password.form');
            Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot-password');
            Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset.form');
            Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

            Route::middleware('auth')->group(function (): void {
                Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
                Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
                Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password.form');
                Route::post('/change-password', [AuthController::class, 'changeOwnPassword'])->name('change-password.update');
                Route::get('/force-password', [ForcePasswordChangeController::class, 'show'])->name('force-password.form');
                Route::post('/force-password', [ForcePasswordChangeController::class, 'update'])->name('force-password.update');
            });
        });

        Route::middleware(['auth', 'force.password.change'])->prefix('admin')->name('admin.')->group(function (): void {
            Route::get('/users/manage', [UserAdministrationController::class, 'manage'])->name('users.manage');
            Route::post('/users', [UserAdministrationController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [UserAdministrationController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/status', [UserAdministrationController::class, 'toggleStatus'])->name('users.status.toggle');
            Route::put('/users/{user}/roles', [UserAdministrationController::class, 'assignRoles'])->name('users.roles.assign');
            Route::put('/roles/{role}/permissions', [UserAdministrationController::class, 'syncRolePermissions'])->name('roles.permissions.sync');
        });
    });
