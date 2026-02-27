<?php

declare(strict_types=1);

namespace App\Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDisease extends Model
{
    use HasFactory;

    protected $table = 'employee_diseases';

    protected $guarded = [];
}
