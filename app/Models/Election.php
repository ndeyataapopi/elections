<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'start_time',
        'end_time',
        'user_id',
        'allowed_votes',
        'status',
        'results_status',
        'results_released_at',
        'results_released_by',
        'results_approved_at',
        'results_approval_notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'results_released_at' => 'datetime',
        'results_approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (app()->has('currentTenant')) {
                $model->tenant_id = app('currentTenant')->id;
            }
        });
    }

    /**
     * Update election status based on current state and time
     */
    public function updateStatus(): string
    {
        $now = now();
        
        // Check if election period has ended
        if ($now->greaterThan($this->end_time)) {
            $this->status = 'completed';
            $this->save();
            return 'completed';
        }
        
        // Check if election is in progress (between start and end time)
        if ($now->greaterThanOrEqualTo($this->start_time) && $now->lessThanOrEqualTo($this->end_time)) {
            $this->status = 'in-progress';
            $this->save();
            return 'in-progress';
        }
        
        // Check if we have portfolios, candidates, and voters uploaded
        $hasPortfolios = $this->portfolios()->count() > 0;
        $hasCandidates = $this->candidates()->count() > 0;
        $hasVoters = $this->voters()->count() > 0;
        
        // Check if at least half of candidates have updated their profiles (profile_complete is datetime)
        $totalCandidates = $this->candidates()->count();
        $updatedCandidates = $this->candidates()->whereNotNull('profile_complete')->count();
        $halfCandidatesUpdated = $totalCandidates > 0 && $updatedCandidates >= ($totalCandidates / 2);
        
        // If all requirements met for pending status
        if ($hasPortfolios && $hasCandidates && $hasVoters && $halfCandidatesUpdated) {
            $this->status = 'pending';
            $this->save();
            return 'pending';
        }
        
        // Default to draft
        $this->status = 'draft';
        $this->save();
        return 'draft';
    }
    
    /**
     * Check if election is active for voting (in-progress status and within time window)
     */
    public function isActive(): bool
    {
        // First update status based on current time
        $this->updateStatus();
        
        $now = now();
        $started = $now->greaterThanOrEqualTo($this->start_time);
        $ended = $now->greaterThan($this->end_time);
        $inProgress = $this->status === 'in-progress';

        return $started && !$ended && $inProgress;
    }
    
    /**
     * Check if candidates can edit their profiles.
     * Allowed when election has not started (draft or pending status).
     */
    public function canEditProfiles(): bool
    {
        $this->updateStatus();

        // Allow editing for draft and pending (before election starts)
        return in_array($this->status, ['draft', 'pending']);
    }
    
    /**
     * Get human-readable status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending' => 'Pending',
            'in-progress' => 'In Progress',
            'completed' => 'Completed',
            default => ucfirst($this->status ?? 'Unknown'),
        };
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function voters()
    {
        return $this->hasMany(Voter::class);
    }

    public function approvalAdmins()
    {
        return $this->hasMany(ElectionApprovalAdmin::class);
    }

    public function resultsReleasedBy()
    {
        return $this->belongsTo(User::class, 'results_released_by');
    }

    /**
     * Check if election results can be released (must be completed)
     */
    public function canReleaseResults(): bool
    {
        $this->updateStatus();
        return $this->status === 'completed' && in_array($this->results_status, ['pending', 'rejected']);
    }

    /**
     * Get human-readable results status label
     */
    public function getResultsStatusLabel(): string
    {
        return match($this->results_status) {
            'pending' => 'Pending Release',
            'released' => 'Released - Awaiting Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst($this->results_status ?? 'Unknown'),
        };
    }
}
