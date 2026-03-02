<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Actions;

use App\Modules\Employee\Models\Employee;
use App\Modules\Team\Models\Team;
use App\Modules\Workflow\Models\ShiftSwapRequest;
use Illuminate\Validation\ValidationException;

final readonly class ResolveShiftSwapCoordinatorAction {
    /**
     * @throws ValidationException
     */
    public function execute(ShiftSwapRequest $shiftSwapRequest): Employee {
        /** @var Team|null $team */
        $team = Team::query()
            ->where('is_active', true)
            ->whereNotNull('coordinator_employee_id')
            ->whereHas('activeMembers', fn($query) => $query->where('employee_id', $shiftSwapRequest->requester_id))
            ->whereHas('activeMembers', fn($query) => $query->where('employee_id', $shiftSwapRequest->target_id))
            ->with('coordinator.user')
            ->first();

        $coordinator = $team?->coordinator;

        if ($coordinator === null || !$coordinator->is_active) {
            throw ValidationException::withMessages([
                'shift_swap_request' => 'No existe coordinador activo para aprobar este cambio de turno.',
            ]);
        }

        return $coordinator;
    }
}
