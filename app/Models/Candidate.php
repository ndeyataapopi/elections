<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Candidate extends Model
{
    protected $fillable = [
        'tenant_id',
        'election_id',
        'portfolio_id',
        'staff_number',
        'first_name',
        'last_name',
        'gender',
        'email',
        'phone',
        'job_title',
        'manifesto',
        'photo',
        'profile_complete',
        'edit_token_hash',
    ];

    protected static function booted()
    {
        // Generate short 5-char token for SMS-friendly URLs
        static::creating(function ($candidate) {
            $plainToken = Str::random(5);
            $candidate->edit_token_hash = hash('sha256', $plainToken);
        });
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Accessor for plain_edit_token - returns null since we only store the hash
     * Token must be regenerated when needed for notifications
     */
    public function getPlainEditTokenAttribute()
    {
        return null;
    }
}
