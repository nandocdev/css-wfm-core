<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function (): void {
    });
