<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Contracts\Core\NotificationDispatcherContract;
use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Workflow\Models\LeaveRequest;
use App\Modules\Workflow\Models\ShiftSwapRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class CreateShiftSwapRequestAction {
    public function __construct(
        private NotificationDispatcherContract $notificationDispatcher,
    ) {
    }

    /**
     * @param  array{target_id:int, swap_date:string}  $payload
     * @throws ValidationException
     */
    public function execute(int $userId, array $payload): ShiftSwapRequest {
        /** @var Employee|null $requester */
        $requester = Employee::query()->with('user')->where('user_id', $userId)->where('is_active', true)->first();

        if ($requester === null) {
            throw ValidationException::withMessages([
                'employee' => 'No existe empleado activo vinculado al usuario autenticado.',
            ]);
        }

        /** @var Employee|null $target */
        $target = Employee::query()->with('user')->whereKey($payload['target_id'])->where('is_active', true)->first();

        if ($target === null) {
            throw ValidationException::withMessages([
                'target_id' => 'El empleado destino no existe o está inactivo.',
            ]);
        }

        if ((int) $requester->id === (int) $target->id) {
            throw ValidationException::withMessages([
                'target_id' => 'No puedes solicitar cambio de turno contigo mismo.',
            ]);
        }

        if ((int) $requester->position_id !== (int) $target->position_id) {
            throw ValidationException::withMessages([
                'target_id' => 'El cambio de turno solo puede realizarse con empleados del mismo rol.',
            ]);
        }

        $swapDate = Carbon::parse($payload['swap_date'])->toDateString();

        $requesterAssignment = $this->resolveAssignment((int) $requester->id, $swapDate);
        $targetAssignment = $this->resolveAssignment((int) $target->id, $swapDate);

        if ($requesterAssignment === null || $targetAssignment === null) {
            throw ValidationException::withMessages([
                'swap_date' => 'Ambos empleados deben tener horario asignado y publicado en la fecha seleccionada.',
            ]);
        }

        if ((int) $requesterAssignment->weekly_schedule_id !== (int) $targetAssignment->weekly_schedule_id) {
            throw ValidationException::withMessages([
                'swap_date' => 'Los empleados deben pertenecer a la misma planificación semanal para intercambiar turnos.',
            ]);
        }

        $this->validateNoActiveExceptions((int) $requester->id, $swapDate, 'requester_id');
        $this->validateNoActiveExceptions((int) $target->id, $swapDate, 'target_id');

        $alreadyRequested = ShiftSwapRequest::query()
            ->whereDate('swap_date', $swapDate)
            ->whereIn('status', ['pending', 'accepted'])
            ->where(function ($query) use ($requester, $target): void {
                $query->where(function ($nested) use ($requester, $target): void {
                    $nested->where('requester_id', $requester->id)->where('target_id', $target->id);
                })->orWhere(function ($nested) use ($requester, $target): void {
                    $nested->where('requester_id', $target->id)->where('target_id', $requester->id);
                });
            })
            ->exists();

        if ($alreadyRequested) {
            throw ValidationException::withMessages([
                'swap_date' => 'Ya existe una solicitud activa de cambio para este par de empleados en esa fecha.',
            ]);
        }

        /** @var ShiftSwapRequest $shiftSwapRequest */
        $shiftSwapRequest = DB::transaction(function () use ($requester, $target, $requesterAssignment, $targetAssignment, $swapDate): ShiftSwapRequest {
            return ShiftSwapRequest::query()->create([
                'requester_id' => $requester->id,
                'target_id' => $target->id,
                'weekly_schedule_id' => $requesterAssignment->weekly_schedule_id,
                'swap_date' => $swapDate,
                'requester_assignment_id' => $requesterAssignment->id,
                'target_assignment_id' => $targetAssignment->id,
                'status' => 'pending',
            ]);
        });

        if ($target->user !== null) {
            $this->notificationDispatcher->dispatch(
                [$target->user],
                'Solicitud de cambio de turno',
                'Recibiste una solicitud de cambio de turno pendiente de tu respuesta.',
                '/workflow/leaves',
            );
        }

        return $shiftSwapRequest;
    }

    private function resolveAssignment(int $employeeId, string $swapDate): ?WeeklyScheduleAssignment {
        /** @var WeeklyScheduleAssignment|null $assignment */
        $assignment = WeeklyScheduleAssignment::query()
            ->with('weeklySchedule')
            ->where('employee_id', $employeeId)
            ->whereHas('weeklySchedule', function ($query) use ($swapDate): void {
                $query->where('status', 'published')
                    ->whereDate('week_start_date', '<=', $swapDate)
                    ->whereDate('week_end_date', '>=', $swapDate);
            })
            ->first();

        return $assignment;
    }

    /**
     * @throws ValidationException
     */
    private function validateNoActiveExceptions(int $employeeId, string $swapDate, string $field): void {
        $hasException = LeaveRequest::query()
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->whereDate('start_datetime', '<=', $swapDate)
            ->whereDate('end_datetime', '>=', $swapDate)
            ->exists();

        if ($hasException) {
            throw ValidationException::withMessages([
                $field => 'El empleado seleccionado tiene una excepción activa en la fecha indicada.',
            ]);
        }
    }
}
