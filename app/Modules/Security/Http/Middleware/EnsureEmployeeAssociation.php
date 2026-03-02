<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Middleware;

use App\Modules\Security\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final class EnsureEmployeeAssociation {
    /**
     * @var array<int, string>
     */
    private array $systemRoles = [
        'Administrador',
        'Analista WFM',
    ];

    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null || $this->isSystemRoleUser($user) || $this->hasEmployeeAssociation($user)) {
            return $next($request);
        }

        $message = 'Tu usuario no está asociado a un empleado activo. Contacta a un administrador del sistema.';

        if ($request->expectsJson()) {
            return new JsonResponse(['message' => $message], Response::HTTP_FORBIDDEN);
        }

        return redirect()
            ->route('security.auth.profile')
            ->withErrors(['employee' => $message]);
    }

    private function isSystemRoleUser(User $user): bool {
        $userRoleNames = $user->getRoleNames()->all();

        foreach ($this->systemRoles as $systemRole) {
            if (in_array($systemRole, $userRoleNames, true)) {
                return true;
            }
        }

        return false;
    }

    private function hasEmployeeAssociation(User $user): bool {
        return DB::table('employees')
            ->where('user_id', $user->getKey())
            ->where('is_active', true)
            ->exists();
    }
}
