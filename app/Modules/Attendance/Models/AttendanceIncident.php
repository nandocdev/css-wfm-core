<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Models;

use App\Modules\Core\Models\IncidentType;
use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceIncident extends Model {
    use HasFactory;

    protected $table = 'attendance_incidents';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'incident_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function incidentType(): BelongsTo {
        return $this->belongsTo(IncidentType::class);
    }
}
