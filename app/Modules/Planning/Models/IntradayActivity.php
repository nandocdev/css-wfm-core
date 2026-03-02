<?php

declare(strict_types=1);

namespace App\Modules\Planning\Models;

use App\Modules\Security\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntradayActivity extends Model {
    use HasFactory;

    protected $table = 'intraday_activities';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'activity_date' => 'date',
            'max_participants' => 'integer',
        ];
    }

    public function weeklySchedule(): BelongsTo {
        return $this->belongsTo(WeeklySchedule::class);
    }

    public function assignments(): HasMany {
        return $this->hasMany(IntradayActivityAssignment::class);
    }

    public function createdBy(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }
}
