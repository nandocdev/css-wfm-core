<?php

declare(strict_types=1);

namespace App\Modules\Schedule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTemplate extends Model
{
    use HasFactory;

    protected $table = 'break_templates';

    protected $guarded = [];
}
