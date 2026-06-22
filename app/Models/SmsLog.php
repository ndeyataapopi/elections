<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'recipient_type',
        'recipient_id',
        'recipient_phone',
        'recipient_name',
        'sms_type',
        'message',
        'election_id',
        'status',
        'provider_response',
        'error_message',
        'sent_at',
        'ip_address',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (app()->has('currentTenant')) {
                $model->tenant_id = app('currentTenant')->id;
            }
        });
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function recipient()
    {
        return $this->morphTo();
    }
}
