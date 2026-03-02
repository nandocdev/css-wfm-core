<?php

declare(strict_types=1);

namespace App\Modules\Planning\Actions;

use App\Modules\Planning\Models\EmployeeBreakOverride;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final readonly class CreateEmployeeBreakOverrideAction {
    /**
     * @param  array{employee_id:int, lunch_start:string, lunch_end:string, break_start:string, break_end:string, reason:string, effective_from:string, effective_to:string|null}  $payload
     * @throws ValidationException
     */
    public function execute(array $payload, int $createdBy): EmployeeBreakOverride {
        $effectiveFrom = CarbonImmutable::parse($payload['effective_from'])->toDateString();
        $effectiveTo = $payload['effective_to'] === null
            ? null
            : CarbonImmutable::parse($payload['effective_to'])->toDateString();

        $hasOverlap = EmployeeBreakOverride::query()
            ->where('employee_id', $payload['employee_id'])
            ->whereDate('effective_from', '<=', $effectiveTo ?? '9999-12-31')
            ->where(function ($query) use ($effectiveFrom): void {
                $query
                    ->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $effectiveFrom);
            })
            ->exists();

        if ($hasOverlap) {
            throw ValidationException::withMessages([
                'effective_from' => 'Ya existe una sobrescritura de pausas que se cruza con la vigencia indicada.',
            ]);
        }

        /** @var EmployeeBreakOverride $override */
        $override = DB::transaction(function () use ($payload, $createdBy): EmployeeBreakOverride {
            return EmployeeBreakOverride::query()->create([
                'employee_id' => $payload['employee_id'],
                'lunch_start' => $payload['lunch_start'],
                'lunch_end' => $payload['lunch_end'],
                'break_start' => $payload['break_start'],
                'break_end' => $payload['break_end'],
                'reason' => $payload['reason'],
                'effective_from' => $payload['effective_from'],
                'effective_to' => $payload['effective_to'],
                'created_by' => $createdBy,
            ]);
        });

        return $override;
    }
}
