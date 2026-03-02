<?php

declare(strict_types=1);

use App\Modules\Core\Http\Controllers\NotificationCenterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->name('core.')
    ->group(function (): void {
        Route::get('/notifications', [NotificationCenterController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{notification}/read', [NotificationCenterController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationCenterController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
