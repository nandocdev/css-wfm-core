<?php

declare(strict_types=1);

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseaseType extends Model
{
    use HasFactory;

    protected $table = 'disease_types';

    protected $guarded = [];
}
