<?php

declare(strict_types=1);

namespace App\Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDependent extends Model
{
    use HasFactory;

    protected $table = 'employee_dependents';

    protected $guarded = [];
}
