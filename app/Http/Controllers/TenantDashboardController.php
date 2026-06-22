<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Voter;

class TenantDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenantId = app()->has('currentTenant') ? app('currentTenant')->id : null;
        
        // Count users (client admins) for the tenant
        $usersCount = User::where('tenant_id', $tenantId)->where('role', 'client_admin')->count();
        
        // Count elections for the tenant
        $electionsCount = Election::where('tenant_id', $tenantId)->count();
        
        // Count candidates for the tenant
        $candidatesCount = Candidate::where('tenant_id', $tenantId)->distinct()->count('id');
        
        // Count voters for the tenant
        $votersCount = Voter::where('tenant_id', $tenantId)->distinct()->count('id');
        
        // Get elections with their statistics for charts
        $elections = Election::where('tenant_id', $tenantId)
            ->withCount(['candidates', 'voters'])
            ->get();
        
        // Prepare data for charts
        $electionNames = $elections->pluck('name')->toArray();
        $electionCandidates = $elections->pluck('candidates_count')->toArray();
        $electionVoters = $elections->pluck('voters_count')->toArray();
        
        return view('tenant.dashboard', compact(
            'usersCount',
            'electionsCount',
            'candidatesCount',
            'votersCount',
            'elections',
            'electionNames',
            'electionCandidates',
            'electionVoters'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
