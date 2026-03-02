<?php

declare(strict_types=1);

namespace App\Modules\Organization\Models;

use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model {
    use HasFactory;

    protected $table = 'departments';

    protected $guarded = [];

    public function directorate(): BelongsTo {
        return $this->belongsTo(Directorate::class);
    }

    public function positions(): HasMany {
        return $this->hasMany(Position::class);
    }

    public function employees(): HasMany {
        return $this->hasMany(Employee::class);
    }
}
