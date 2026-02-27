<?php

declare(strict_types=1);

namespace App\Modules\Security\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Security\Actions\LoginAction;
use App\Modules\Security\Actions\LogoutAction;
use App\Modules\Security\Actions\ResetPasswordAction;
use App\Modules\Security\Actions\SendPasswordResetLinkAction;
use App\Modules\Security\Http\Requests\ForgotPasswordRequest;
use App\Modules\Security\Http\Requests\LoginRequest;
use App\Modules\Security\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

final class AuthController extends Controller {
    public function __construct(
        private LoginAction $loginAction,
        private LogoutAction $logoutAction,
        private SendPasswordResetLinkAction $sendPasswordResetLinkAction,
        private ResetPasswordAction $resetPasswordAction,
    ) {
    }

    public function showLoginForm(): View {
        return view('security::auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse {
        $user = $this->loginAction->execute($request);

        if ($user->force_password_change) {
            return redirect()->to(URL::to('/security/auth/force-password'));
        }

        return redirect()->intended('/');
    }

    public function logout(Request $request): RedirectResponse {
        $this->logoutAction->execute($request);

        return redirect()->to(URL::to('/security/auth/login'));
    }

    public function showForgotPasswordForm(): View {
        return view('security::auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse {
        $this->sendPasswordResetLinkAction->execute((string) $request->validated('email'));

        return back()->with('status', __($this->passwordStatusKey(Password::RESET_LINK_SENT)));
    }

    public function showResetForm(string $token, Request $request): View {
        return view('security::auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse {
        $status = $this->resetPasswordAction->execute(
            (string) $request->validated('email'),
            (string) $request->validated('token'),
            (string) $request->validated('password'),
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($this->passwordStatusKey($status))]);
        }

        return redirect()->to(URL::to('/security/auth/login'))->with('status', __($this->passwordStatusKey($status)));
    }

    private function passwordStatusKey(string $status): string {
        return match ($status) {
            Password::PASSWORD_RESET => 'passwords.reset',
            Password::INVALID_TOKEN => 'passwords.token',
            Password::INVALID_USER => 'passwords.user',
            default => 'passwords.sent',
        };
    }
}
