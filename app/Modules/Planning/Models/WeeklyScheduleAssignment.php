<?php

declare(strict_types=1);

namespace App\Modules\Planning\Models;

use App\Modules\Employee\Models\Employee;
use App\Modules\Schedule\Models\BreakTemplate;
use App\Modules\Schedule\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyScheduleAssignment extends Model {
    use HasFactory;

    protected $table = 'weekly_schedule_assignments';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'is_custom_break' => 'boolean',
        ];
    }

    public function weeklySchedule(): BelongsTo {
        return $this->belongsTo(WeeklySchedule::class);
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function schedule(): BelongsTo {
        return $this->belongsTo(Schedule::class);
    }

    public function breakTemplate(): BelongsTo {
        return $this->belongsTo(BreakTemplate::class);
    }
}
