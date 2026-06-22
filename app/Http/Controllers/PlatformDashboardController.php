<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Voter;
use App\Models\User;
use App\Models\EmailLog;
use App\Models\SmsLog;
use App\Models\Vote;
use App\Models\Ballot;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlatformDashboardController extends Controller
{
    /**
     * Display platform dashboard with real statistics.
     */
    public function index()
    {
        // Core Statistics
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'total_elections' => Election::count(),
            'active_elections' => Election::whereIn('status', ['in-progress', 'pending'])->count(),
            'completed_elections' => Election::where('status', 'completed')->count(),
            'total_candidates' => Candidate::count(),
            'total_voters' => Voter::count(),
            'total_users' => User::count(),
            'total_votes_cast' => Vote::count(),
            'total_ballots' => Ballot::count(),
        ];

        // Email Statistics
        $emailStats = [
            'total_sent' => EmailLog::count(),
            'successful' => EmailLog::where('status', 'sent')->count(),
            'failed' => EmailLog::where('status', 'failed')->count(),
            'pending' => EmailLog::where('status', 'pending')->count(),
            'today' => EmailLog::whereDate('created_at', today())->count(),
            'this_month' => EmailLog::whereMonth('created_at', now()->month)->count(),
        ];

        // SMS Statistics
        $smsStats = [
            'total_sent' => SmsLog::count(),
            'successful' => SmsLog::where('status', 'sent')->count(),
            'failed' => SmsLog::where('status', 'failed')->count(),
            'pending' => SmsLog::where('status', 'pending')->count(),
            'today' => SmsLog::whereDate('created_at', today())->count(),
            'this_month' => SmsLog::whereMonth('created_at', now()->month)->count(),
        ];

        // Cost Calculations (example pricing - adjust as needed)
        $pricing = [
            'email' => 0.001, // $0.001 per email
            'sms' => 0.05,    // $0.05 per SMS
        ];

        $costs = [
            'email_cost' => $emailStats['total_sent'] * $pricing['email'],
            'sms_cost' => $smsStats['total_sent'] * $pricing['sms'],
            'total_cost' => ($emailStats['total_sent'] * $pricing['email']) + ($smsStats['total_sent'] * $pricing['sms']),
        ];

        // Recent Activity
        $recentElections = Election::with('tenant')
            ->latest()
            ->take(5)
            ->get();

        $recentTenants = Tenant::withCount(['elections', 'users'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly Activity Chart Data (last 6 months)
        $monthlyData = $this->getMonthlyActivityData();

        // Election Status Distribution
        $electionStatusData = Election::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top Tenants by Activity
        $topTenants = Tenant::withCount(['elections', 'voters', 'candidates'])
            ->orderByDesc('elections_count')
            ->take(10)
            ->get();

        // System Health
        $systemHealth = [
            'failed_emails_percentage' => $emailStats['total_sent'] > 0
                ? round(($emailStats['failed'] / $emailStats['total_sent']) * 100, 2)
                : 0,
            'failed_sms_percentage' => $smsStats['total_sent'] > 0
                ? round(($smsStats['failed'] / $smsStats['total_sent']) * 100, 2)
                : 0,
        ];

        return view('platform.dashboard', compact(
            'stats',
            'emailStats',
            'smsStats',
            'costs',
            'pricing',
            'recentElections',
            'recentTenants',
            'monthlyData',
            'electionStatusData',
            'topTenants',
            'systemHealth'
        ));
    }

    /**
     * Get monthly activity data for charts.
     */
    private function getMonthlyActivityData()
    {
        $months = [];
        $emails = [];
        $sms = [];
        $elections = [];
        $votes = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            $emails[] = EmailLog::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $sms[] = SmsLog::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $elections[] = Election::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $votes[] = Vote::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'months' => $months,
            'emails' => $emails,
            'sms' => $sms,
            'elections' => $elections,
            'votes' => $votes,
        ];
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
