<?php

declare(strict_types=1);

namespace App\Modules\Planning\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyScheduleAssignment extends Model
{
    use HasFactory;

    protected $table = 'weekly_schedule_assignments';

    protected $guarded = [];
}
