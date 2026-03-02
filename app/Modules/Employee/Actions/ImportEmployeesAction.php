<?php

declare(strict_types=1);

namespace App\Modules\Employee\Actions;

use App\Modules\Core\Models\EmploymentStatus;
use App\Modules\Core\Models\Township;
use App\Modules\Employee\Models\Employee;
use App\Modules\Organization\Models\Department;
use App\Modules\Organization\Models\Position;
use App\Modules\Security\Models\User;
use App\Modules\Team\Models\Team;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

final readonly class ImportEmployeesAction {
    /**
     * @var array<int, string>
     */
    private array $requiredHeaders;

    public function __construct(
        private CreateEmployeeAction $createEmployeeAction,
    ) {
        $this->requiredHeaders = [
            'user_id',
            'username',
            'first_name',
            'last_name',
            'email',
            'birth_date',
            'township_id',
            'position_id',
            'employment_status_id',
            'hire_date',
        ];
    }

    /**
     * @return array{imported:int, skipped:int, errors:array<int, string>}
     */
    public function execute(UploadedFile $file): array {
        $handle = fopen($file->getRealPath() ?: '', 'rb');

        if ($handle === false) {
            return [
                'imported' => 0,
                'skipped' => 0,
                'errors' => ['No se pudo leer el archivo CSV.'],
            ];
        }

        $header = fgetcsv($handle);

        if (!is_array($header)) {
            fclose($handle);

            return [
                'imported' => 0,
                'skipped' => 0,
                'errors' => ['El archivo CSV no tiene encabezados válidos.'],
            ];
        }

        $header = array_map(static fn($item): string => trim((string) $item), $header);
        $missingHeaders = array_values(array_diff($this->requiredHeaders, $header));

        if ($missingHeaders !== []) {
            fclose($handle);

            return [
                'imported' => 0,
                'skipped' => 0,
                'errors' => ['Faltan columnas obligatorias: ' . implode(', ', $missingHeaders)],
            ];
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        $seenEmployeeNumbers = [];
        $seenUsernames = [];
        $seenEmails = [];

        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if (!is_array($row) || count($row) !== count($header)) {
                $skipped++;
                $errors[] = "Línea {$line}: formato de columnas inválido.";
                continue;
            }

            $payload = $this->normalizeRow(array_combine($header, $row) ?: []);
            $rowErrors = $this->validateRow($payload, $seenEmployeeNumbers, $seenUsernames, $seenEmails);

            if ($rowErrors !== []) {
                $skipped++;
                $errors[] = "Línea {$line}: " . implode(' | ', $rowErrors);
                continue;
            }

            try {
                $this->createEmployeeAction->execute($payload);
                $imported++;
            } catch (\Throwable $throwable) {
                $skipped++;
                $errors[] = "Línea {$line}: error al importar ({$throwable->getMessage()}).";
            }
        }

        fclose($handle);

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeRow(array $row): array {
        $payload = [];

        foreach ($row as $key => $value) {
            $payload[(string) $key] = is_string($value) ? trim($value) : $value;
        }

        $payload['email'] = isset($payload['email']) ? mb_strtolower((string) $payload['email']) : null;
        $payload['is_active'] = $this->toBool($payload['is_active'] ?? '1');
        $payload['is_manager'] = $this->toBool($payload['is_manager'] ?? '0');

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<int, string> $seenEmployeeNumbers
     * @param array<int, string> $seenUsernames
     * @param array<int, string> $seenEmails
     * @return array<int, string>
     */
    private function validateRow(array &$payload, array &$seenEmployeeNumbers, array &$seenUsernames, array &$seenEmails): array {
        $errors = [];

        $employeeNumber = (string) ($payload['employee_number'] ?? '');
        $username = (string) ($payload['username'] ?? '');
        $firstName = (string) ($payload['first_name'] ?? '');
        $lastName = (string) ($payload['last_name'] ?? '');
        $email = (string) ($payload['email'] ?? '');
        $userId = (int) ($payload['user_id'] ?? 0);
        $townshipId = (int) ($payload['township_id'] ?? 0);
        $positionId = (int) ($payload['position_id'] ?? 0);
        $employmentStatusId = (int) ($payload['employment_status_id'] ?? 0);
        $departmentId = (int) ($payload['department_id'] ?? 0);
        $parentId = (int) ($payload['parent_id'] ?? 0);
        $teamId = (int) ($payload['team_id'] ?? 0);

        if ($userId <= 0 || !User::query()->whereKey($userId)->exists()) {
            $errors[] = 'user_id no existe.';
        }

        if ($userId > 0 && Employee::query()->where('user_id', $userId)->exists()) {
            $errors[] = 'El usuario ya tiene ficha de empleado.';
        }

        if ($username === '' || preg_match('/^[a-zA-Z0-9._-]+$/', $username) !== 1) {
            $errors[] = 'username inválido.';
        }

        if ($username !== '' && in_array($username, $seenUsernames, true)) {
            $errors[] = 'username duplicado en el archivo.';
        }

        if ($username !== '' && Employee::query()->where('username', $username)->exists()) {
            $errors[] = 'username ya existe.';
        }

        if ($firstName === '' || $lastName === '') {
            $errors[] = 'first_name y last_name son obligatorios.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'email inválido.';
        }

        if ($email !== '' && in_array($email, $seenEmails, true)) {
            $errors[] = 'email duplicado en el archivo.';
        }

        if ($email !== '' && Employee::query()->where('email', $email)->exists()) {
            $errors[] = 'email ya existe.';
        }

        if ($employeeNumber !== '') {
            if (in_array($employeeNumber, $seenEmployeeNumbers, true)) {
                $errors[] = 'employee_number duplicado en el archivo.';
            }

            if (Employee::query()->where('employee_number', $employeeNumber)->exists()) {
                $errors[] = 'employee_number ya existe.';
            }
        } else {
            $payload['employee_number'] = null;
        }

        if (!$this->isValidDate((string) ($payload['birth_date'] ?? null))) {
            $errors[] = 'birth_date inválida (formato esperado: YYYY-MM-DD).';
        }

        if (!$this->isValidDate((string) ($payload['hire_date'] ?? null))) {
            $errors[] = 'hire_date inválida (formato esperado: YYYY-MM-DD).';
        }

        if ($townshipId <= 0 || !Township::query()->whereKey($townshipId)->exists()) {
            $errors[] = 'township_id no existe.';
        }

        $position = Position::query()->with('department')->find($positionId);

        if ($position === null) {
            $errors[] = 'position_id no existe.';
        }

        if ($employmentStatusId <= 0 || !EmploymentStatus::query()->whereKey($employmentStatusId)->exists()) {
            $errors[] = 'employment_status_id no existe.';
        }

        if ($departmentId > 0) {
            if (!Department::query()->whereKey($departmentId)->exists()) {
                $errors[] = 'department_id no existe.';
            }

            if ($position !== null && (int) $position->department_id !== $departmentId) {
                $errors[] = 'department_id no coincide con el position_id.';
            }
        } elseif ($position !== null) {
            $payload['department_id'] = (int) $position->department_id;
        } else {
            $payload['department_id'] = null;
        }

        if ($parentId > 0 && !Employee::query()->whereKey($parentId)->exists()) {
            $errors[] = 'parent_id no existe.';
        }

        if ($teamId > 0 && !Team::query()->whereKey($teamId)->exists()) {
            $errors[] = 'team_id no existe.';
        }

        if (isset($payload['salary']) && $payload['salary'] !== '' && !is_numeric($payload['salary'])) {
            $errors[] = 'salary debe ser numérico.';
        }

        if ($errors === []) {
            if ($employeeNumber !== '') {
                $seenEmployeeNumbers[] = $employeeNumber;
            }

            $seenUsernames[] = $username;
            $seenEmails[] = $email;
        }

        $payload['user_id'] = $userId;
        $payload['township_id'] = $townshipId;
        $payload['position_id'] = $positionId;
        $payload['employment_status_id'] = $employmentStatusId;
        $payload['parent_id'] = $parentId > 0 ? $parentId : null;
        $payload['team_id'] = $teamId > 0 ? $teamId : null;
        $payload['salary'] = isset($payload['salary']) && $payload['salary'] !== '' ? (float) $payload['salary'] : null;

        return $errors;
    }

    private function toBool(mixed $value): bool {
        $normalized = mb_strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'si', 'sí', 'yes'], true);
    }

    private function isValidDate(?string $date): bool {
        if ($date === null || trim($date) === '') {
            return false;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Throwable) {
            return false;
        }

        return $parsed !== false && $parsed->format('Y-m-d') === $date;
    }
}
