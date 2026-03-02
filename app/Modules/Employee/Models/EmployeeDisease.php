<?php

declare(strict_types=1);

namespace App\Modules\Employee\Models;

use App\Modules\Core\Models\DiseaseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDisease extends Model {
    use HasFactory;

    protected $table = 'employee_diseases';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'diagnosis_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }

    public function diseaseType(): BelongsTo {
        return $this->belongsTo(DiseaseType::class);
    }
}
