<?php

declare(strict_types=1);

namespace App\Modules\Core\Models;

use App\Modules\Attendance\Models\AttendanceIncident;
use App\Modules\Workflow\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentType extends Model {
    use HasFactory;

    protected $table = 'incident_types';

    protected $guarded = [];

    public function leaveRequests(): HasMany {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attendanceIncidents(): HasMany {
        return $this->hasMany(AttendanceIncident::class);
    }
}
