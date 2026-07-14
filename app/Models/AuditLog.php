<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'action',
        'user_type',
        'user_id',
        'meta',
    ];

}
