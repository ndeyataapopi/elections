<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'subdomain',
        'logo',
        'status',
        'enable_candidate_email_notifications',
        'enable_voter_email_notifications',
        'enable_candidate_sms_notifications',
        'enable_voter_sms_notifications',
        'candidate_email_template',
        'candidate_sms_template',
        'voter_email_template',
        'voter_sms_template',
    ];

    protected $casts = [
        'enable_candidate_email_notifications' => 'boolean',
        'enable_voter_email_notifications' => 'boolean',
        'enable_candidate_sms_notifications' => 'boolean',
        'enable_voter_sms_notifications' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function elections()
    {
        return $this->hasMany(Election::class);
    }

    public function voters()
    {
        return $this->hasMany(Voter::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
