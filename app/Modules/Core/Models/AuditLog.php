<?php

declare(strict_types=1);

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model {
    use HasFactory;

    public $timestamps = false;

    public const CREATED_AT = 'created_at';

    protected $table = 'audit_logs';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'before' => 'array',
            'after' => 'array',
            'created_at' => 'datetime',
            'ip_address' => 'string',
        ];
    }
}
