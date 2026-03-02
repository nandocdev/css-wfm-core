<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Models;

use App\Modules\Core\Models\IncidentType;
use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveRequest extends Model {
    use HasFactory;

    protected $table = 'leave_requests';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
        ];
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function incidentType(): BelongsTo {
        return $this->belongsTo(IncidentType::class);
    }

    public function approvals(): HasMany {
        return $this->hasMany(LeaveRequestApproval::class);
    }
}
