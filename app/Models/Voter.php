<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Voter extends Model
{
    protected $fillable = [
        'tenant_id',
        'election_id',
        'staff_number',
        'first_name',
        'last_name',
        'gender',
        'email',
        'phone',
        'token_hash',
        'has_voted',
        'voted_at',
    ];

    protected static function booted()
    {
        // Generate short 5-char token for SMS-friendly URLs
        static::creating(function ($voter) {
            $plainToken = Str::random(5);
            $voter->token_hash = hash('sha256', $plainToken);
        });
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function ballot()
    {
        return $this->hasOne(Ballot::class);
    }

    /**
     * Accessor for plain_token - returns null since we only store the hash
     * Token must be regenerated when needed for notifications
     */
    public function getPlainTokenAttribute()
    {
        return null;
    }
}
