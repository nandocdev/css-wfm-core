<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission {
    protected $guard_name = 'web';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];
}
