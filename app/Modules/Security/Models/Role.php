<?php

declare(strict_types=1);

namespace App\Modules\Security\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole {
    protected $guard_name = 'web';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'code',
        'hierarchy_level',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'hierarchy_level' => 'integer',
        ];
    }
}
