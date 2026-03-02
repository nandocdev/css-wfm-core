<?php

declare(strict_types=1);

namespace App\Modules\Team\Models;

use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model {
    use HasFactory;

    protected $table = 'teams';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function members(): HasMany {
        return $this->hasMany(TeamMember::class);
    }

    public function activeMembers(): HasMany {
        return $this->hasMany(TeamMember::class)->where('is_active', true);
    }

    public function coordinator(): BelongsTo {
        return $this->belongsTo(Employee::class, 'coordinator_employee_id');
    }
}
