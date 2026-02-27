<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Middleware;

use App\Modules\Security\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePasswordChanged {
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null || !$user->force_password_change) {
            return $next($request);
        }

        if ($request->routeIs('security.auth.force-password.*', 'security.auth.logout')) {
            return $next($request);
        }

        return new RedirectResponse(route('security.auth.force-password.form'));
    }
}
