<?php

declare(strict_types=1);

namespace App\Modules\Security\Actions;

use App\Modules\Security\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;

final readonly class ChangeOwnPasswordAction {
    public function __construct(
        private Hasher $hasher,
        private DatabaseManager $databaseManager,
    ) {
    }

    public function execute(User $user, string $plainPassword, string $currentSessionId): void {
        $this->databaseManager->transaction(function () use ($user, $plainPassword): void {
            $user->forceFill([
                'password' => $this->hasher->make($plainPassword),
                'force_password_change' => false,
                'remember_token' => Str::random(60),
            ])->save();
        });

        if (config('session.driver') !== 'database') {
            return;
        }

        $table = (string) config('session.table', 'sessions');
        $connection = config('session.connection');

        $this->databaseManager
            ->connection(is_string($connection) ? $connection : null)
            ->table($table)
            ->where('user_id', $user->getKey())
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }
}
