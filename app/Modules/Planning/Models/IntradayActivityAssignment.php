<?php

declare(strict_types=1);

namespace App\Modules\Planning\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntradayActivityAssignment extends Model
{
    use HasFactory;

    protected $table = 'intraday_activity_assignments';

    protected $guarded = [];
}
