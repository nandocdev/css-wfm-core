<?php

declare(strict_types=1);

namespace App\Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDependent extends Model {
    use HasFactory;

    protected $table = 'employee_dependents';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'birth_date' => 'date',
            'is_dependent' => 'boolean',
        ];
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }
}
