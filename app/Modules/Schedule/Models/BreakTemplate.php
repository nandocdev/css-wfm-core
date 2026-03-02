<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Models;

use App\Modules\Planning\Models\WeeklyScheduleAssignment;
use App\Modules\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BreakTemplate extends Model {
    use HasFactory;

    protected $table = 'break_templates';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'lunch_start' => 'datetime:H:i',
            'lunch_end' => 'datetime:H:i',
            'break_start' => 'datetime:H:i',
            'break_end' => 'datetime:H:i',
            'is_active' => 'boolean',
        ];
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function weeklyAssignments(): HasMany {
        return $this->hasMany(WeeklyScheduleAssignment::class);
    }
}
