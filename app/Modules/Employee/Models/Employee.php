<?php

declare(strict_types=1);

namespace App\Modules\Employee\Models;

use App\Modules\Core\Models\EmploymentStatus;
use App\Modules\Core\Models\Township;
use App\Modules\Organization\Models\Department;
use App\Modules\Organization\Models\Position;
use App\Modules\Security\Models\User;
use App\Modules\Team\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model {
    use HasFactory;

    protected $table = 'employees';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'birth_date' => 'date',
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
            'is_manager' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo {
        return $this->belongsTo(Position::class);
    }

    public function township(): BelongsTo {
        return $this->belongsTo(Township::class);
    }

    public function employmentStatus(): BelongsTo {
        return $this->belongsTo(EmploymentStatus::class);
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function descendants(): HasMany {
        return $this->children()->with('descendants', 'position.department.directorate');
    }

    public function dependents(): HasMany {
        return $this->hasMany(EmployeeDependent::class);
    }

    public function disabilities(): HasMany {
        return $this->hasMany(EmployeeDisability::class);
    }

    public function diseases(): HasMany {
        return $this->hasMany(EmployeeDisease::class);
    }

    public function teamMemberships(): HasMany {
        return $this->hasMany(TeamMember::class);
    }
}
