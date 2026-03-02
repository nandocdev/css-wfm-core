<?php

declare(strict_types=1);

namespace App\Modules\Core\Observers;

use App\Modules\Core\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class CriticalModelAuditObserver {
    /**
     * @var array<int, string>
     */
    private array $redactedFields = [
        'password',
        'remember_token',
        'token',
    ];

    public function created(Model $model): void {
        $this->writeAuditLog($model, 'created', null, $this->snapshot($model));
    }

    public function updated(Model $model): void {
        $changes = array_keys($model->getChanges());
        $changes = array_values(array_filter($changes, static fn(string $field): bool => $field !== 'updated_at'));

        if ($changes === []) {
            return;
        }

        $before = [];
        $after = [];

        foreach ($changes as $field) {
            $before[$field] = $model->getOriginal($field);
            $after[$field] = $model->getAttribute($field);
        }

        $this->writeAuditLog($model, 'updated', $this->sanitize($before), $this->sanitize($after));
    }

    public function deleted(Model $model): void {
        $this->writeAuditLog($model, 'deleted', $this->snapshot($model), null);
    }

    public function restored(Model $model): void {
        $this->writeAuditLog($model, 'restored', null, $this->snapshot($model));
    }

    /**
     * @param array<string, mixed>|null $before
     * @param array<string, mixed>|null $after
     */
    private function writeAuditLog(Model $model, string $action, ?array $before, ?array $after): void {
        AuditLog::query()->create([
            'user_id' => $this->resolveUserId(),
            'entity_type' => class_basename($model),
            'entity_id' => (int) $model->getKey(),
            'action' => $action,
            'before' => $before,
            'after' => $after,
            'ip_address' => $this->resolveIpAddress(),
            'created_at' => now(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(Model $model): array {
        return $this->sanitize($model->getAttributes());
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function sanitize(array $data): array {
        foreach ($this->redactedFields as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = '***';
            }
        }

        return $data;
    }

    private function resolveUserId(): ?int {
        if (!Auth::check()) {
            return null;
        }

        $userId = Auth::id();

        return is_int($userId) ? $userId : (is_numeric($userId) ? (int) $userId : null);
    }

    private function resolveIpAddress(): ?string {
        if (!app()->bound('request')) {
            return null;
        }

        $ip = request()->ip();

        return is_string($ip) ? $ip : null;
    }
}
