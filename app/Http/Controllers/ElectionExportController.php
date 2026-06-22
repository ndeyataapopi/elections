<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Election;
use App\Models\Vote;
use App\Models\Ballot;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ElectionExportController extends Controller
{
    /**
     * Export election results as PDF.
     */
    public function exportPdf($id)
    {
        $election = Election::with(['portfolios.candidates.votes', 'voters', 'approvalAdmins.user'])->findOrFail($id);
        
        // Calculate results
        $results = $this->calculateResults($election);
        
        // Generate PDF view
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('tenant.reports.export_pdf', [
            'election' => $election,
            'results' => $results,
            'generated_at' => now()->format('F d, Y H:i:s'),
        ]);
        
        return $pdf->download("election-{$election->id}-results.pdf");
    }

    /**
     * Export election results as CSV.
     */
    public function exportCsv($id)
    {
        $election = Election::with(['portfolios.candidates.votes', 'voters'])->findOrFail($id);
        
        // Calculate results
        $results = $this->calculateResults($election);
        
        $filename = "election-{$election->id}-results.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        
        $callback = function () use ($election, $results) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Election Results Report']);
            fputcsv($file, ['Generated:', now()->format('F d, Y H:i:s')]);
            fputcsv($file, []);
            
            // Election Info
            fputcsv($file, ['Election Information']);
            fputcsv($file, ['Name:', $election->name]);
            fputcsv($file, ['Description:', $election->description]);
            fputcsv($file, ['Status:', $election->status]);
            fputcsv($file, ['Start Date:', $election->start_date?->format('F d, Y H:i') ?? 'N/A']);
            fputcsv($file, ['End Date:', $election->end_date?->format('F d, Y H:i') ?? 'N/A']);
            fputcsv($file, []);
            
            // Summary
            fputcsv($file, ['Summary']);
            fputcsv($file, ['Total Voters:', $results['total_voters']]);
            fputcsv($file, ['Votes Cast:', $results['votes_cast']]);
            fputcsv($file, ['Turnout:', $results['turnout_percentage'] . '%']);
            fputcsv($file, []);
            
            // Results by Portfolio
            fputcsv($file, ['Detailed Results by Portfolio']);
            fputcsv($file, []);
            
            foreach ($results['portfolio_results'] as $portfolioResult) {
                fputcsv($file, ['Portfolio:', $portfolioResult['portfolio']->name]);
                fputcsv($file, ['Candidate', 'Votes', 'Percentage', 'Status']);
                
                foreach ($portfolioResult['candidates'] as $candidate) {
                    fputcsv($file, [
                        $candidate['name'],
                        $candidate['votes'],
                        $candidate['percentage'] . '%',
                        $candidate['is_winner'] ? 'Winner' : '',
                    ]);
                }
                
                fputcsv($file, ['Winner:', $portfolioResult['winner'] ? $portfolioResult['winner']['name'] : 'N/A']);
                fputcsv($file, []);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate election results.
     */
    private function calculateResults(Election $election): array
    {
        $totalVoters = $election->voters()->count();
        $votesCast = Ballot::whereHas('votes', function ($q) use ($election) {
            $q->where('election_id', $election->id);
        })->distinct('voter_id')->count('voter_id');
        
        $turnoutPercentage = $totalVoters > 0 ? round(($votesCast / $totalVoters) * 100, 2) : 0;
        
        $portfolioResults = [];
        
        foreach ($election->portfolios as $portfolio) {
            $candidateResults = [];
            $winner = null;
            $maxVotes = 0;
            
            foreach ($portfolio->candidates as $candidate) {
                $voteCount = $candidate->votes()->where('election_id', $election->id)->count();
                $totalVotesForPortfolio = Vote::where('portfolio_id', $portfolio->id)
                    ->where('election_id', $election->id)
                    ->count();
                
                $percentage = $totalVotesForPortfolio > 0 
                    ? round(($voteCount / $totalVotesForPortfolio) * 100, 2) 
                    : 0;
                
                $candidateData = [
                    'candidate' => $candidate,
                    'name' => $candidate->full_name,
                    'votes' => $voteCount,
                    'percentage' => $percentage,
                    'is_winner' => false,
                ];
                
                $candidateResults[] = $candidateData;
                
                // Track winner
                if ($voteCount > $maxVotes) {
                    $maxVotes = $voteCount;
                    $winner = $candidateData;
                }
            }
            
            // Mark winner
            if ($winner) {
                foreach ($candidateResults as &$cr) {
                    if ($cr['candidate']->id === $winner['candidate']->id) {
                        $cr['is_winner'] = true;
                    }
                }
            }
            
            $portfolioResults[] = [
                'portfolio' => $portfolio,
                'candidates' => $candidateResults,
                'winner' => $winner,
                'total_votes' => array_sum(array_column($candidateResults, 'votes')),
            ];
        }
        
        return [
            'total_voters' => $totalVoters,
            'votes_cast' => $votesCast,
            'turnout_percentage' => $turnoutPercentage,
            'portfolio_results' => $portfolioResults,
        ];
    }
}
