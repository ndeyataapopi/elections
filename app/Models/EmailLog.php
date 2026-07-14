<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'recipient_type',
        'recipient_id',
        'recipient_email',
        'recipient_name',
        'email_type',
        'subject',
        'content',
        'election_id',
        'status',
        'error_message',
        'sent_at',
        'ip_address',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function recipient()
    {
        return $this->morphTo();
    }
}
