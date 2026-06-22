<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Ballot;
use App\Models\Vote;
use App\Models\Voter;
use App\Models\AuditLog;

class VoteController extends Controller
{
    //
    public function show($token)
    {
        $hashed = hash('sha256', $token);
        $voter = Voter::where('token_hash', $hashed)->first();

        // If voter not found by token, they may have already voted (token invalidated)
        if (!$voter) {
            return view('public.voting-error', [
                'error' => 'This voting link is no longer valid. If you have already voted, thank you for participating!',
            ]);
        }

        // Check if voter already voted (do this first, before checking election status)
        if ($voter->has_voted) {
            return view('public.voting-thank-you');
        }

        $election = $voter->election()->with(['portfolios.candidates'])->first();

        // Debug election status
        $election->updateStatus();
        
        $now = now();
        Log::debug('VoteController show election check', [
            'election_id' => $election->id,
            'status' => $election->status,
            'now' => $now->toDateTimeString(),
            'start_time' => $election->start_time?->toDateTimeString(),
            'end_time' => $election->end_time?->toDateTimeString(),
            'isActive' => $election->isActive(),
        ]);

        if (!$election->isActive()) {
            $errorMsg = 'This election is not currently active for voting. ';

            if ($now->greaterThan($election->end_time)) {
                $errorMsg .= 'Voting ended at ' . $election->end_time->format('F j, Y \a\t g:i A') . '.';
            } elseif ($now->lessThan($election->start_time)) {
                $errorMsg .= 'Voting starts at ' . $election->start_time->format('F j, Y \a\t g:i A') . '.';
            } else {
                $errorMsg .= 'Current status: ' . $election->getStatusLabel() . '. Voting is only allowed during "In Progress" status.';
            }

            Log::debug('Election not active', [
                'now' => $now->toDateTimeString(),
                'start_time' => $election->start_time?->toDateTimeString(),
                'end_time' => $election->end_time?->toDateTimeString(),
                'status' => $election->status,
                'election_id' => $election->id,
            ]);

            return view('public.voting-error', [
                'error' => $errorMsg,
            ]);
        }

        return view('public.vote', compact('voter', 'election', 'token'));
    }

    public function submit(Request $request, $token)
    {
        Log::debug('Vote submission received', [
            'token' => $token,
            'request_data' => $request->all(),
            'votes_data' => $request->votes,
        ]);

        $hashed = hash('sha256', $token);
        $voter = Voter::where('token_hash', $hashed)->firstOrFail();

        if ($voter->has_voted)
        {
            abort(403);
        }

        if (Ballot::where('voter_id', $voter->id)->exists())
        {
            abort(403, 'You have already submitted your ballot.');
        }

        // Validate votes data is present and properly formatted
        if (!$request->has('votes') || !is_array($request->votes)) {
            Log::error('Invalid votes data format', [
                'voter_id' => $voter->id,
                'request_data' => $request->all(),
            ]);
            return redirect()->back()->with('error', 'Invalid vote data. Please try again.');
        }

        $ballot = Ballot::create([
            'tenant_id' => $voter->tenant_id,
            'election_id' => $voter->election_id,
            'voter_id' => $voter->id,
            'submitted_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $votesCreated = 0;

        foreach ($request->votes as $portfolio_id => $candidate_ids) {
            // Handle both array (checkbox) and single value (radio) formats
            $candidate_ids = is_array($candidate_ids) ? $candidate_ids : [$candidate_ids];

            foreach ($candidate_ids as $candidate_id) {
                Log::debug('Processing candidate_id', [
                    'raw_candidate_id' => $candidate_id,
                    'type' => gettype($candidate_id),
                    'length' => is_string($candidate_id) ? strlen($candidate_id) : 'N/A',
                ]);

                // AGGRESSIVE: Reject any non-numeric or suspicious values
                if (!is_numeric($candidate_id)) {
                    Log::error('Non-numeric candidate_id rejected', [
                        'candidate_id' => $candidate_id,
                        'type' => gettype($candidate_id),
                        'voter_id' => $voter->id,
                    ]);
                    continue;
                }

                // Cast to integer
                $candidate_id = (int) $candidate_id;

                // Validate positive integer
                if ($candidate_id <= 0) {
                    Log::error('Invalid candidate_id (zero or negative)', [
                        'candidate_id' => $candidate_id,
                        'voter_id' => $voter->id,
                    ]);
                    continue;
                }

                // Validate candidate exists and belongs to this portfolio
                $candidate = \App\Models\Candidate::where('id', $candidate_id)
                    ->where('portfolio_id', $portfolio_id)
                    ->first();

                if (!$candidate) {
                    Log::error('Candidate not found or invalid portfolio', [
                        'candidate_id' => $candidate_id,
                        'portfolio_id' => $portfolio_id,
                    ]);
                    continue;
                }

                Log::debug('Creating vote record', [
                    'portfolio_id' => $portfolio_id,
                    'candidate_id' => $candidate_id,
                    'ballot_id' => $ballot->id,
                ]);

                try {
                    Vote::create([
                        'ballot_id' => $ballot->id,
                        'candidate_id' => $candidate_id,
                        'portfolio_id' => $portfolio_id,
                        'election_id' => $voter->election_id,
                        'tenant_id' => $voter->tenant_id,
                    ]);
                    $votesCreated++;
                } catch (\Exception $e) {
                    Log::error('Failed to create vote record', [
                        'error' => $e->getMessage(),
                        'candidate_id' => $candidate_id,
                        'portfolio_id' => $portfolio_id,
                    ]);
                }
            }
        }

        Log::debug('Vote submission completed', [
            'votes_created' => $votesCreated,
            'ballot_id' => $ballot->id,
        ]);

        $voter->update([
            'has_voted' => true,
            'voted_at' => now(),
            'token_hash' => null, // Invalidate the voting token - link can no longer be used
        ]);

        // Log EVERYTHING important:
        // Election activated
        // Election closed
        // CSV uploaded
        // Candidate approved

        AuditLog::create([
            'tenant_id' => $voter->tenant_id,
            'action' => 'vote_cast',
            'user_type' => 'voter',
            'user_id' => $voter->id,
            'meta' => json_encode([
                'ip' => request()->ip(),
                'election_id' => $voter->election_id
            ])
        ]);

        return view('public.voting-thank-you');
    }
}
