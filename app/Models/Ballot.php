<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ballot extends Model
{
    //
    protected $fillable = [
        'tenant_id',
        'election_id',
        'voter_id',
        'submitted_at',
        'ip_address',
        'user_agent'
    ];

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
}
