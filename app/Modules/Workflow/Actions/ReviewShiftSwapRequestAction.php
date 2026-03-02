<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Contracts\Core\NotificationDispatcherContract;
use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Workflow\Models\ShiftSwapApproval;
use App\Modules\Workflow\Models\ShiftSwapRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class ReviewShiftSwapRequestAction {
    public function __construct(
        private ResolveShiftSwapCoordinatorAction $resolveShiftSwapCoordinatorAction,
        private NotificationDispatcherContract $notificationDispatcher,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function execute(ShiftSwapRequest $shiftSwapRequest, int $approverUserId, string $action, ?string $comments): void {
        if ($shiftSwapRequest->status !== 'accepted') {
            throw ValidationException::withMessages([
                'shift_swap_request' => 'Solo las solicitudes aceptadas por el empleado destino pueden revisarse.',
            ]);
        }

        /** @var Employee|null $actor */
        $actor = Employee::query()->with('user')->where('user_id', $approverUserId)->where('is_active', true)->first();

        if ($actor === null) {
            throw ValidationException::withMessages([
                'shift_swap_request' => 'No existe empleado activo para el usuario autenticado.',
            ]);
        }

        $expectedApprover = $this->resolveShiftSwapCoordinatorAction->execute($shiftSwapRequest);

        if ((int) $expectedApprover->id !== (int) $actor->id && !$actor->user?->hasAnyRole(['Administrador', 'Analista WFM'])) {
            throw ValidationException::withMessages([
                'shift_swap_request' => 'No tienes autorización para revisar este cambio de turno.',
            ]);
        }

        if ($shiftSwapRequest->approvals()->exists()) {
            throw ValidationException::withMessages([
                'shift_swap_request' => 'La solicitud ya tiene una revisión registrada (flujo de un paso).',
            ]);
        }

        $decision = $action === 'approved' ? 'approved' : 'rejected';

        DB::transaction(function () use ($shiftSwapRequest, $actor, $decision, $comments): void {
            ShiftSwapApproval::query()->create([
                'shift_swap_request_id' => $shiftSwapRequest->id,
                'approver_id' => $actor->id,
                'step' => 1,
                'action' => $decision,
                'comments' => $comments,
                'acted_at' => now(),
            ]);

            if ($decision === 'approved') {
                $requesterAssignment = WeeklyScheduleAssignment::query()->whereKey($shiftSwapRequest->requester_assignment_id)->first();
                $targetAssignment = WeeklyScheduleAssignment::query()->whereKey($shiftSwapRequest->target_assignment_id)->first();

                if ($requesterAssignment === null || $targetAssignment === null) {
                    throw ValidationException::withMessages([
                        'shift_swap_request' => 'No se encontraron las asignaciones de turno para completar el cambio.',
                    ]);
                }

                $requesterScheduleId = (int) $requesterAssignment->schedule_id;
                $targetScheduleId = (int) $targetAssignment->schedule_id;

                $requesterAssignment->schedule_id = $targetScheduleId;
                $requesterAssignment->save();

                $targetAssignment->schedule_id = $requesterScheduleId;
                $targetAssignment->save();
            }

            $shiftSwapRequest->status = $decision;
            $shiftSwapRequest->save();
        });

        $shiftSwapRequest->loadMissing(['requester.user', 'target.user']);

        $recipients = collect([$shiftSwapRequest->requester?->user, $shiftSwapRequest->target?->user])->filter()->values()->all();

        if (count($recipients) > 0) {
            $this->notificationDispatcher->dispatch(
                $recipients,
                $decision === 'approved' ? 'Cambio de turno aprobado' : 'Cambio de turno rechazado',
                $decision === 'approved'
                ? 'El coordinador aprobó el cambio de turno solicitado.'
                : 'El coordinador rechazó el cambio de turno solicitado.',
                '/workflow/leaves',
            );
        }
    }
}
