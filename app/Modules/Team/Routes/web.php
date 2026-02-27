<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('team')
    ->name('team.')
    ->group(function (): void {
    });
