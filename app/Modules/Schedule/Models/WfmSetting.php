<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WfmSetting extends Model {
    use HasFactory;

    protected $table = 'wfm_settings';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'late_tolerance_minutes' => 'integer',
            'early_leave_tolerance_minutes' => 'integer',
            'approval_threshold_hours' => 'integer',
            'max_overtime_minutes' => 'integer',
            'allow_force_approval' => 'boolean',
        ];
    }
}
