<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'employee.association'])
    ->prefix('attendance')
    ->name('attendance.')
    ->group(function (): void {
    });
