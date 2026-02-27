<?php

declare(strict_types=1);

namespace App\Modules\Workflow\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSwapApproval extends Model
{
    use HasFactory;

    protected $table = 'shift_swap_approvals';

    protected $guarded = [];
}
