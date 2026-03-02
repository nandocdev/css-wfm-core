<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Contracts\Core\NotificationDispatcherContract;
use App\Modules\Employee\Models\Employee;
use App\Modules\Workflow\Models\ShiftSwapRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class RespondShiftSwapRequestAction {
    public function __construct(
        private ResolveShiftSwapCoordinatorAction $resolveShiftSwapCoordinatorAction,
        private NotificationDispatcherContract $notificationDispatcher,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function execute(ShiftSwapRequest $shiftSwapRequest, int $actorUserId, string $action, ?string $comments): void {
        if ($shiftSwapRequest->status !== 'pending') {
            throw ValidationException::withMessages([
                'shift_swap_request' => 'Solo las solicitudes pendientes pueden responderse.',
            ]);
        }

        /** @var Employee|null $actor */
        $actor = Employee::query()->with('user')->where('user_id', $actorUserId)->where('is_active', true)->first();

        if ($actor === null || (int) $actor->id !== (int) $shiftSwapRequest->target_id) {
            throw ValidationException::withMessages([
                'shift_swap_request' => 'Solo el empleado destino puede responder esta solicitud.',
            ]);
        }

        $status = $action === 'accepted' ? 'accepted' : 'rejected';

        DB::transaction(function () use ($shiftSwapRequest, $status): void {
            $shiftSwapRequest->status = $status;
            $shiftSwapRequest->target_response_at = now();
            $shiftSwapRequest->save();
        });

        $shiftSwapRequest->loadMissing(['requester.user', 'target.user']);

        $requesterUser = $shiftSwapRequest->requester?->user;

        if ($requesterUser !== null) {
            $this->notificationDispatcher->dispatch(
                [$requesterUser],
                'Respuesta de cambio de turno',
                $status === 'accepted'
                ? 'Tu solicitud de cambio fue aceptada por el empleado destino y espera revisión del coordinador.'
                : 'Tu solicitud de cambio fue rechazada por el empleado destino.',
                '/workflow/leaves',
            );
        }

        if ($status === 'accepted') {
            $coordinator = $this->resolveShiftSwapCoordinatorAction->execute($shiftSwapRequest);

            if ($coordinator->user !== null) {
                $this->notificationDispatcher->dispatch(
                    [$coordinator->user],
                    'Cambio de turno pendiente de aprobación',
                    'Existe una solicitud de cambio de turno aceptada por ambas partes pendiente de revisión.',
                    '/workflow/leaves',
                );
            }
        }
    }
}
