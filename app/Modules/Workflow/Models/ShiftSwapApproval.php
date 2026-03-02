<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Models;

use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftSwapApproval extends Model {
    use HasFactory;

    protected $table = 'shift_swap_approvals';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'step' => 'integer',
            'acted_at' => 'datetime',
        ];
    }

    public function shiftSwapRequest(): BelongsTo {
        return $this->belongsTo(ShiftSwapRequest::class);
    }

    public function approver(): BelongsTo {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
}
