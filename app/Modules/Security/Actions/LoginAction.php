<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Http\Requests\LoginRequest;
use App\Modules\Security\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;

final readonly class LoginAction
{
    public function __construct(
        private StatefulGuard $guard,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function execute(LoginRequest $request): User
    {
        $credentials = [
            'email' => (string) $request->validated('email'),
            'password' => (string) $request->validated('password'),
            'is_active' => true,
        ];

        $remember = (bool) $request->boolean('remember');

        if (! $this->guard->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no son válidas o la cuenta está inactiva.',
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = $this->guard->user();
        $user->forceFill(['last_login_at' => now()])->save();

        return $user;
    }
}
