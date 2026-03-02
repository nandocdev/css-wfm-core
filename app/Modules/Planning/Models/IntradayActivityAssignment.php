<?php

declare(strict_types=1);

namespace App\Modules\Planning\Models;

use App\Modules\Employee\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntradayActivityAssignment extends Model {
    use HasFactory;

    protected $table = 'intraday_activity_assignments';

    protected $guarded = [];

    public function intradayActivity(): BelongsTo {
        return $this->belongsTo(IntradayActivity::class);
    }

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class);
    }
}
