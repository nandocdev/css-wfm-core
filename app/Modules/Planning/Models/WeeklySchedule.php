<?php

declare(strict_types=1);

namespace App\Modules\Planning\Models;

use App\Modules\Security\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklySchedule extends Model {
    use HasFactory;

    protected $table = 'weekly_schedules';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'week_start_date' => 'date',
            'week_end_date' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function assignments(): HasMany {
        return $this->hasMany(WeeklyScheduleAssignment::class);
    }

    public function publishedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'published_by');
    }
}
