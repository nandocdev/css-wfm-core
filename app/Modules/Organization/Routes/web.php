<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('organization')
    ->name('organization.')
    ->group(function (): void {
    });
