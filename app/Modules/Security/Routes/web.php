<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('security')
    ->name('security.')
    ->group(function (): void {
    });
