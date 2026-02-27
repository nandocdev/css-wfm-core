<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('workflow')
    ->name('workflow.')
    ->group(function (): void {
    });
