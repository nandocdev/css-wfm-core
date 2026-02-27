<?php

declare(strict_types=1);

namespace App\Modules\Planning\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntradayActivity extends Model
{
    use HasFactory;

    protected $table = 'intraday_activities';

    protected $guarded = [];
}
