<?php

declare(strict_types=1);

namespace App\Modules\Team\Models;

use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model {
    use HasFactory;

    protected $table = 'team_members';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }
}
