<?php

declare(strict_types=1);

namespace App\Modules\Organization\Models;

use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model {
    use HasFactory;

    protected $table = 'positions';

    protected $guarded = [];

    public function department(): BelongsTo {
        return $this->belongsTo(Department::class);
    }

    public function employees(): HasMany {
        return $this->hasMany(Employee::class);
    }
}
