<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Election;
use App\Models\Portfolio;
use App\Models\Voter;
use App\Models\EmailLog;
use App\Models\SmsLog;
use App\Models\Ballot;
use App\Services\SMSService;
use Session;
use Auth;


class VoterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user_tenant = Auth::user()->tenant_id;
        $elections = Election::where('tenant_id',$user_tenant)->withCount('portfolios')->withCount('candidates')->withCount('voters')->get();
        return view('tenant.voters.index', compact('elections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('tenant.voters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        return redirect()->route('voters.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $voter = Voter::with(['election'])->findOrFail($id);
        return view('tenant.voters.edit', compact('voter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'staff_number' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $voter = Voter::findOrFail($id);
            
            $voter->update([
                'staff_number' => $request->staff_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            return redirect()->route('voters.view', $voter->election_id)
                ->with('success', 'Voter updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating voter: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function view($id)
    {
        //
        $election = Election::with(['voters'])->findOrFail($id);
        return view('tenant.voters.view', compact('election'));
    }

    /**
     * Show the upload form for the specified election.
     */
    public function upload($id)
    {
        //
        $election = Election::findOrFail($id);
        return view('tenant.voters.upload', compact('election'));
    }

    /**
     * Handle file upload for the specified election.
     */
    public function uploadFile(Request $request, $id)
    {
        //
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('excel_file');
            $voters = $this->parseExcelFile($file);
            
            // Store voters in session for next step
            session(['uploaded_voters' => $voters, 'election_id' => $id]);
            
            return redirect()->route('voters.review', $id)
                ->with('success', 'File uploaded successfully! Please review voters before saving.');
                
        } catch (\Exception $e) {
            return redirect()->route('voters.upload', $id)
                ->with('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    /**
     * Show review form for uploaded voters.
     */
    public function review($id)
    {
        //
        if (!session('uploaded_voters')) {
            return redirect()->route('voters.upload', $id)
                ->with('error', 'No uploaded voters found. Please upload a file first.');
        }

        $election = Election::findOrFail($id);
        $voters = session('uploaded_voters');
        
        return view('tenant.voters.review', compact('election', 'voters'));
    }

    /**
     * Save uploaded voters.
     */
    public function saveVoters(Request $request, $id)
    {
        //
        try {
            $voters = session('uploaded_voters');
            $election = Election::findOrFail($id);
            
            foreach ($voters as $voterData) {
                $voter = array_merge($voterData, [
                    'election_id' => $election->id,
                    'tenant_id' => $election->tenant_id,
                    'has_voted' => false,
                ]);
                
                Voter::create($voter);
            }
            
            // Clear session
            session()->forget(['uploaded_voters', 'election_id']);
            
            return redirect()->route('voters.view', $id)
                ->with('success', 'Voters uploaded successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error saving voters: ' . $e->getMessage());
        }
    }

    /**
     * Display individual voter details (read-only).
     */
    public function details($id)
    {
        //
        $voter = Voter::with(['election'])->findOrFail($id);
        return view('tenant.voters.details', compact('voter'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $voter = Voter::findOrFail($id);
            $electionId = $voter->election_id;
            $voter->delete();
            
            return redirect()->route('voters.view', $electionId)
                ->with('success', 'Voter deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting voter: ' . $e->getMessage());
        }
    }

    /**
     * Parse Excel file and return voter data.
     */
    private function parseExcelFile($file)
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $voters = [];
            
            // Start from row 2 (skip header)
            $rowIterator = $worksheet->getRowIterator();
            $rowIterator->resetStart(2);
            
            foreach ($rowIterator as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                
                $rowData = [];
                $colIndex = 0;
                
                foreach ($cellIterator as $cell) {
                    $rowData[$colIndex] = $cell->getValue();
                    $colIndex++;
                }
                
                // Skip empty rows
                if (empty(array_filter($rowData))) {
                    continue;
                }
                
                // Map columns to voter fields (adjust indices based on your Excel format)
                $voter = [
                    'staff_number' => $rowData[0] ?? null,
                    'first_name' => $rowData[1] ?? null,
                    'last_name' => $rowData[2] ?? null,
                    'gender' => $rowData[3] ?? null,
                    'phone' => $rowData[4] ?? null,
                    'email' => $rowData[5] ?? null,
                ];
                
                // Validate required fields
                if (!empty($voter['staff_number']) && !empty($voter['first_name']) && !empty($voter['last_name'])) {
                    $voters[] = $voter;
                }
            }
            
            if (empty($voters)) {
                throw new \Exception('No valid voter data found in the Excel file. Please check the file format.');
            }
            
            return $voters;
            
        } catch (\Exception $e) {
            throw new \Exception('Error parsing Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Notify all voters for an election via email and SMS.
     *
     * Flow: Validate Settings → Send to Each Voter → Return Result
     */
    public function notify($electionId)
    {
        try {
            // 1. Load election with voters
            $election = Election::with('voters')->findOrFail($electionId);
            $voters = $election->voters;

            if ($voters->isEmpty()) {
                return $this->notificationResponse('No voters found for this election.', 'warning');
            }

            // 2. Check notification settings
            $settings = $this->getNotificationSettings('voter');
            if (!$settings['email_enabled'] && !$settings['sms_enabled']) {
                return $this->notificationResponse('Both email and SMS notifications are disabled in settings.', 'warning');
            }

            // 3. Send notifications to each voter
            $smsService = new SMSService();
            $stats = ['email_sent' => 0, 'email_failed' => 0, 'sms_sent' => 0, 'sms_failed' => 0, 'already_voted' => 0];

            foreach ($voters as $voter) {
                // Skip if already voted
                if ($voter->has_voted) {
                    $stats['already_voted']++;
                    continue;
                }

                $token = $this->generateVoterToken($voter);
                $votingUrl = route('vote.cast', ['token' => $token]);

                // Send Email
                if ($settings['email_enabled'] && $voter->email) {
                    $this->sendVoterEmail($voter, $election, $votingUrl, $token)
                        ? $stats['email_sent']++
                        : $stats['email_failed']++;
                } elseif ($settings['email_enabled']) {
                    $stats['email_failed']++;
                }

                // Send SMS
                if ($settings['sms_enabled'] && $voter->phone && $smsService->isConfigured()) {
                    $this->sendVoterSMS($voter, $election, $votingUrl, $smsService)
                        ? $stats['sms_sent']++
                        : $stats['sms_failed']++;
                }
            }

            // 4. Build and return response
            $message = $this->buildVoterNotificationMessage($stats, $settings);
            return $this->notificationResponse($message, 'success');

        } catch (\Exception $e) {
            Log::error('Bulk voter notification failed', ['election_id' => $electionId, 'error' => $e->getMessage()]);
            return $this->notificationResponse('Failed to send notifications: ' . $e->getMessage(), 'error', 500);
        }
    }

    /**
     * Notify a single voter via email and SMS.
     */
    public function notifySingle($voterId)
    {
        try {
            // 1. Load voter with election
            $voter = Voter::with('election')->findOrFail($voterId);
            $election = $voter->election;

            // 2. Check notification settings
            $settings = $this->getNotificationSettings('voter');
            if (!$settings['email_enabled'] && !$settings['sms_enabled']) {
                return $this->notificationResponse('Both email and SMS notifications are disabled in settings.', 'warning');
            }

            // 3. Skip if already voted
            if ($voter->has_voted) {
                return $this->notificationResponse('This voter has already cast their vote.', 'warning');
            }

            // 4. Prepare and send notifications
            $token = $this->generateVoterToken($voter);
            $votingUrl = route('vote.cast', ['token' => $token]);
            $smsService = new SMSService();
            $results = [];

            if ($settings['email_enabled']) {
                $results[] = $voter->email
                    ? ($this->sendVoterEmail($voter, $election, $votingUrl, $token) ? 'email sent' : 'email failed')
                    : 'email skipped (no email)';
            }

            if ($settings['sms_enabled'] && $voter->phone && $smsService->isConfigured()) {
                $results[] = $this->sendVoterSMS($voter, $election, $votingUrl, $smsService)
                    ? 'SMS sent'
                    : 'SMS failed';
            }

            // 5. Return response
            $name = $voter->first_name . ' ' . $voter->last_name;
            $message = "Notification to {$name}: " . implode(', ', $results) . '.';
            return $this->notificationResponse($message, 'success');

        } catch (\Exception $e) {
            Log::error('Single voter notification failed', ['voter_id' => $voterId, 'error' => $e->getMessage()]);
            return $this->notificationResponse('Failed to send notification: ' . $e->getMessage(), 'error', 500);
        }
    }

    /**
     * Get notification settings from tenant.
     */
    private function getNotificationSettings(string $type): array
    {
        $tenant = tenant();
        return [
            'email_enabled' => $tenant->{"enable_{$type}_email_notifications"} ?? false,
            'sms_enabled' => $tenant->{"enable_{$type}_sms_notifications"} ?? false,
        ];
    }

    /**
     * Generate and save a 5-character voting token for a voter.
     */
    private function generateVoterToken($voter): string
    {
        $token = \Illuminate\Support\Str::random(5);
        $voter->token_hash = hash('sha256', $token);
        $voter->save();
        return $token;
    }

    /**
     * Build a human-readable notification summary message for voters.
     */
    private function buildVoterNotificationMessage(array $stats, array $settings): string
    {
        $parts = [];

        if ($settings['email_enabled']) {
            $parts[] = "{$stats['email_sent']} emails sent";
            if ($stats['email_failed'] > 0) {
                $parts[] = "{$stats['email_failed']} failed";
            }
        }

        if ($settings['sms_enabled']) {
            $parts[] = "{$stats['sms_sent']} SMS sent";
            if ($stats['sms_failed'] > 0) {
                $parts[] = "{$stats['sms_failed']} SMS failed";
            }
        }

        if ($stats['already_voted'] > 0) {
            $parts[] = "{$stats['already_voted']} skipped (already voted)";
        }

        return 'Notifications: ' . implode(', ', $parts);
    }

    /**
     * Return notification response (JSON for AJAX, redirect for regular requests).
     */
    private function notificationResponse(string $message, string $type = 'success', int $httpCode = 200)
    {
        if (request()->ajax() || request()->wantsJson()) {
            $success = $type === 'success';
            $code = $httpCode > 200 ? $httpCode : ($success ? 200 : 400);
            return response()->json(['success' => $success, 'message' => $message], $code);
        }
        return redirect()->back()->with($type, $message);
    }

    /**
     * Send email notification to voter
     */
    private function sendVoterEmail($voter, $election, $votingUrl, $token): bool
    {
        try {
            // Create email log entry
            $emailLog = EmailLog::create([
                'recipient_type' => 'voter',
                'recipient_id' => $voter->id,
                'recipient_email' => $voter->email,
                'recipient_name' => $voter->first_name . ' ' . $voter->last_name,
                'email_type' => 'voting_link',
                'subject' => 'Your Voting Link - ' . ($election->name ?? 'Upcoming Election'),
                'election_id' => $election->id,
                'status' => 'pending',
                'ip_address' => request()->ip(),
            ]);

            // Send the email using view like candidate email (Mailable may have issues)
            Mail::send('emails.voting-link', [
                'voter' => $voter,
                'election' => $election,
                'votingUrl' => $votingUrl,
                'token' => $token,
            ], function ($message) use ($voter, $election) {
                $message->to($voter->email)
                    ->subject('Your Voting Link - ' . ($election->name ?? 'Upcoming Election'));
            });

            // Update log status
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send voter email: ' . $e->getMessage(), [
                'voter_id' => $voter->id,
                'email' => $voter->email,
            ]);

            // Update log with error
            if (isset($emailLog)) {
                $emailLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return false;
        }
    }

    /**
     * Send SMS notification to voter
     */
    private function sendVoterSMS($voter, $election, $votingUrl, SMSService $smsService): bool
    {
        try {
            // SMS with voting link (using short 5-char token)
            $message = sprintf(
                "Hi %s, vote here: %s",
                $voter->first_name,
                $votingUrl
            );

            // Create SMS log entry
            $smsLog = SmsLog::create([
                'recipient_type' => 'voter',
                'recipient_id' => $voter->id,
                'recipient_phone' => $voter->phone,
                'recipient_name' => $voter->first_name . ' ' . $voter->last_name,
                'sms_type' => 'voting_link',
                'message' => $message,
                'election_id' => $election->id,
                'status' => 'pending',
                'ip_address' => request()->ip(),
            ]);

            // Send SMS
            $result = $smsService->send($voter->phone, $message);

            // Update log status
            $smsLog->update([
                'status' => $result['success'] ? 'sent' : 'failed',
                'sent_at' => $result['success'] ? now() : null,
                'provider_response' => json_encode($result['response']),
                'error_message' => $result['error'],
            ]);

            return $result['success'];

        } catch (\Exception $e) {
            Log::error('Failed to send voter SMS: ' . $e->getMessage(), [
                'voter_id' => $voter->id,
                'phone' => $voter->phone,
            ]);

            // Update log with error
            if (isset($smsLog)) {
                $smsLog->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return false;
        }
    }
}
