<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;

final readonly class LogoutAction
{
    public function __construct(
        private StatefulGuard $guard,
    ) {
    }

    public function execute(Request $request): void
    {
        $this->guard->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
