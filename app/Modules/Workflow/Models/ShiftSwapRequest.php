<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Models;

use App\Modules\Employee\Models\Employee;
use App\Modules\Planning\Models\WeeklySchedule;
use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftSwapRequest extends Model {
    use HasFactory;

    protected $table = 'shift_swap_requests';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'swap_date' => 'date',
            'target_response_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo {
        return $this->belongsTo(Employee::class, 'requester_id');
    }

    public function target(): BelongsTo {
        return $this->belongsTo(Employee::class, 'target_id');
    }

    public function weeklySchedule(): BelongsTo {
        return $this->belongsTo(WeeklySchedule::class);
    }

    public function requesterAssignment(): BelongsTo {
        return $this->belongsTo(WeeklyScheduleAssignment::class, 'requester_assignment_id');
    }

    public function targetAssignment(): BelongsTo {
        return $this->belongsTo(WeeklyScheduleAssignment::class, 'target_assignment_id');
    }

    public function approvals(): HasMany {
        return $this->hasMany(ShiftSwapApproval::class);
    }
}
