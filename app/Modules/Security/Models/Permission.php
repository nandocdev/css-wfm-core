<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $guarded = [];
}
