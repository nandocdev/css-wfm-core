<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Models;

use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model {
    use HasFactory;

    protected $table = 'schedules';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'lunch_minutes' => 'integer',
            'break_minutes' => 'integer',
            'total_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function weeklyAssignments(): HasMany {
        return $this->hasMany(WeeklyScheduleAssignment::class);
    }
}
