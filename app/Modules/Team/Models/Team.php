<?php

declare(strict_types=1);

namespace App\Modules\Team\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model {
    use HasFactory;

    protected $table = 'teams';

    protected $guarded = [];

    public function members(): HasMany {
        return $this->hasMany(TeamMember::class);
    }
}
