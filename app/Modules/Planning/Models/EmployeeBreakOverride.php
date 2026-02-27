<?php

declare(strict_types=1);

namespace App\Modules\Planning\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBreakOverride extends Model
{
    use HasFactory;

    protected $table = 'employee_break_overrides';

    protected $guarded = [];
}
