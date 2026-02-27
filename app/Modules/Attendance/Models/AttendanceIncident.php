<?php

declare(strict_types=1);

namespace App\Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceIncident extends Model
{
    use HasFactory;

    protected $table = 'attendance_incidents';

    protected $guarded = [];
}
