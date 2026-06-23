<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Election;
use App\Models\Vote;
use App\Models\Ballot;
use App\Models\ElectionApprovalAdmin;
use App\Mail\ElectionResultsMail;

class ReportController extends Controller
{
    /**
     * Display reports listing with all elections and their status.
     */
    public function index()
    {
        $tenant = tenant();
        $elections = Election::where('tenant_id', $tenant->id)
            ->withCount(['voters', 'candidates', 'portfolios'])
            ->with(['approvalAdmins.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tenant.reports.index', compact('elections'));
    }

    /**
     * Display election results for viewing.
     */
    public function view($id)
    {
        $election = Election::with(['portfolios.candidates.votes', 'approvalAdmins.user'])
            ->findOrFail($id);

        $results = $this->calculateResults($election);

        return view('tenant.reports.view', compact('election', 'results'));
    }

    /**
     * Release election results - send to approval admins.
     */
    public function releaseResults($id)
    {
        try {
            $election = Election::with('approvalAdmins.user')->findOrFail($id);

            // Check if results can be released
            if (!$election->canReleaseResults()) {
                return redirect()->back()
                    ->with('error', 'Results cannot be released. Election must be completed and not already released.');
            }

            // Check if approval admins exist
            if ($election->approvalAdmins->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'No approval administrators configured for this election. Please add approval admins first.');
            }

            // Calculate results
            $results = $this->calculateResults($election);

            // Update election status
            $election->update([
                'results_status' => 'released',
                'results_released_at' => now(),
                'results_released_by' => Auth::id(),
            ]);

            // Send emails to approval admins
            $sentCount = 0;
            $totalAdmins = $election->approvalAdmins->count();

            Log::info('Starting to send results emails', [
                'election_id' => $election->id,
                'total_admins' => $totalAdmins,
                'mail_driver' => config('mail.default'),
            ]);

            foreach ($election->approvalAdmins as $admin) {
                Log::debug('Processing admin', [
                    'admin_id' => $admin->user_id,
                    'has_user' => $admin->user ? true : false,
                    'email' => $admin->user?->email,
                ]);

                if ($admin->user && $admin->user->email) {
                    $approvalUrl = route('reports.approve.view', $election->id);

                    Log::info('Attempting to send email to admin', [
                        'admin_id' => $admin->user_id,
                        'email' => $admin->user->email,
                        'election' => $election->name,
                    ]);

                    try {
                        Mail::to($admin->user->email)
                            ->send(new ElectionResultsMail($election, $admin->user, $results, $approvalUrl));
                        $sentCount++;

                        Log::info('Email sent successfully to admin', [
                            'admin_id' => $admin->user_id,
                            'email' => $admin->user->email,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send results email to admin', [
                            'admin_id' => $admin->user_id,
                            'election_id' => $election->id,
                            'email' => $admin->user->email,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                } else {
                    Log::warning('Admin missing user or email', [
                        'admin_id' => $admin->user_id,
                        'has_user' => $admin->user ? true : false,
                        'has_email' => $admin->user?->email ? true : false,
                    ]);
                }
            }

            Log::info('Finished sending results emails', [
                'election_id' => $election->id,
                'sent_count' => $sentCount,
                'total_admins' => $totalAdmins,
            ]);

            // Build detailed message
            $debugInfo = '';
            if ($sentCount === 0) {
                $debugInfo = ' (No emails were actually sent - check logs for details)';
            }

            return redirect()->route('reports.view', $election->id)
                ->with('success', "Results released successfully! Emails sent to {$sentCount} approval administrator(s).{$debugInfo}");

        } catch (\Exception $e) {
            Log::error('Failed to release election results', [
                'election_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to release results: ' . $e->getMessage());
        }
    }

    /**
     * Show approval page for election results.
     */
    public function approveView($id)
    {
        $election = Election::with(['portfolios.candidates.votes', 'approvalAdmins.user', 'resultsReleasedBy'])
            ->findOrFail($id);

        // Check if user is an approval admin
        $isAdmin = $election->approvalAdmins->contains('user_id', Auth::id());

        if (!$isAdmin && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to approve these results.');
        }

        $results = $this->calculateResults($election);

        return view('tenant.reports.approve', compact('election', 'results', 'isAdmin'));
    }

    /**
     * Process election results approval.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $election = Election::with('approvalAdmins')->findOrFail($id);

            // Check if user is an approval admin
            $isAdmin = $election->approvalAdmins->contains('user_id', Auth::id());

            if (!$isAdmin && !Auth::user()->isAdmin()) {
                abort(403, 'You are not authorized to approve these results.');
            }

            $decision = $request->input('decision');

            if ($decision === 'approve') {
                $election->update([
                    'results_status' => 'approved',
                    'results_approved_at' => now(),
                    'results_approval_notes' => $request->input('notes'),
                ]);

                return redirect()->route('reports.view', $election->id)
                    ->with('success', 'Election results have been approved and certified as free and fair.');
            } else {
                $election->update([
                    'results_status' => 'rejected',
                    'results_approval_notes' => $request->input('notes'),
                ]);

                return redirect()->route('reports.view', $election->id)
                    ->with('warning', 'Election results have been rejected. Please review and release again after corrections.');
            }

        } catch (\Exception $e) {
            Log::error('Failed to process election approval', [
                'election_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to process approval: ' . $e->getMessage());
        }
    }

    /**
     * Calculate comprehensive election results.
     */
    private function calculateResults(Election $election): array
    {
        $totalVoters = $election->voters()->count();
        $totalVotesCast = Ballot::where('election_id', $election->id)->count();
        $turnoutPercentage = $totalVoters > 0 ? round(($totalVotesCast / $totalVoters) * 100, 1) : 0;

        $portfolioResults = [];

        foreach ($election->portfolios as $portfolio) {
            $candidates = [];
            $totalPortfolioVotes = 0;

            foreach ($portfolio->candidates as $candidate) {
                $voteCount = $candidate->votes()->count();
                $totalPortfolioVotes += $voteCount;
            }

            $maxVotes = 0;
            $winners = [];

            foreach ($portfolio->candidates as $candidate) {
                $voteCount = $candidate->votes()->count();
                $percentage = $totalPortfolioVotes > 0 ? round(($voteCount / $totalPortfolioVotes) * 100, 1) : 0;

                if ($voteCount > $maxVotes) {
                    $maxVotes = $voteCount;
                    $winners = [$candidate->id];
                } elseif ($voteCount === $maxVotes && $voteCount > 0) {
                    $winners[] = $candidate->id;
                }

                $candidates[] = [
                    'id' => $candidate->id,
                    'name' => $candidate->first_name . ' ' . $candidate->last_name,
                    'votes' => $voteCount,
                    'percentage' => $percentage,
                    'is_winner' => in_array($candidate->id, $winners),
                ];
            }

            // Sort by votes descending
            usort($candidates, function($a, $b) {
                return $b['votes'] <=> $a['votes'];
            });

            $portfolioResults[] = [
                'id' => $portfolio->id,
                'name' => $portfolio->name,
                'candidates' => $candidates,
                'total_votes' => $totalPortfolioVotes,
            ];
        }

        return [
            'total_voters' => $totalVoters,
            'total_votes_cast' => $totalVotesCast,
            'turnout_percentage' => $turnoutPercentage . '%',
            'total_portfolios' => $election->portfolios->count(),
            'portfolios' => $portfolioResults,
        ];
    }

    /**
     * Placeholder methods for route compatibility.
     */
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function show(string $id) { return $this->view($id); }
    public function edit(string $id) { abort(404); }
    public function update(Request $request, string $id) { abort(404); }
    public function destroy(string $id) { abort(404); }
    public function upload($id) { abort(404); }
}
