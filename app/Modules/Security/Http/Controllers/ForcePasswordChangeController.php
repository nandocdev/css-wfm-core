<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Security\Actions\ForcePasswordChangeAction;
use App\Modules\Security\Http\Requests\ForcePasswordChangeRequest;
use App\Modules\Security\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class ForcePasswordChangeController extends Controller {
    public function __construct(
        private ForcePasswordChangeAction $forcePasswordChangeAction,
    ) {
    }

    public function show(): JsonResponse {
        return response()->json(['message' => 'Debe cambiar su contraseña para continuar.'], Response::HTTP_OK);
    }

    public function update(ForcePasswordChangeRequest $request): RedirectResponse {
        /** @var User $user */
        $user = $request->user();
        $this->forcePasswordChangeAction->execute($user, (string) $request->validated('password'));

        return redirect()->intended('/');
    }
}
