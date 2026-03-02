<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Models;

use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequestApproval extends Model {
    use HasFactory;

    protected $table = 'leave_request_approvals';

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

    public function leaveRequest(): BelongsTo {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function approver(): BelongsTo {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
}
