<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('planning')
    ->name('planning.')
    ->group(function (): void {
    });
