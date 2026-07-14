<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Election;
use App\Models\Portfolio;
use App\Models\Candidate;
use App\Models\EmailLog;
use App\Models\SmsLog;
use App\Mail\CandidateProfileUpdateMail;
use App\Services\SMSService;
use Session;
use Auth;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user_tenant = Auth::user()->tenant_id;
        $elections = Election::where('tenant_id',$user_tenant)
            ->withCount('portfolios')
            ->withCount('candidates')
            ->withCount(['candidates as profiles_complete_count' => function ($query) {
                $query->where('profile_complete', true); // Counts rows where profile_complete is 0
            }])
            ->get();
        return view('tenant.candidates.index', compact('elections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('tenant.candidates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        return redirect()->route('candidates.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $user = Auth::user();
        $candidate = Candidate::where('id', $id)
            ->whereHas('election', function ($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id);
            })
            ->with(['election', 'portfolio'])
            ->firstOrFail();
        return view('tenant.candidates.edit', compact('candidate'));
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
            'job_title' => 'nullable|string|max:255',
            'manifesto' => 'nullable|string|max:2000',
            'portfolio_id' => 'required|exists:portfolios,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        try {
            $user = Auth::user();
            $candidate = Candidate::where('id', $id)
                ->whereHas('election', function ($query) use ($user) {
                    $query->where('tenant_id', $user->tenant_id);
                })
                ->firstOrFail();
            
            $updateData = [
                'staff_number' => $request->staff_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'email' => $request->email,
                'phone' => $request->phone,
                'job_title' => $request->job_title,
                'manifesto' => $request->manifesto,
                'portfolio_id' => $request->portfolio_id,
                'profile_complete' => true,
            ];

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = time() . '_' . $candidate->staff_number . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('candidates/photos', $photoName, 'public');
                $updateData['photo'] = $photoPath;
                
                // Delete old photo if exists
                if ($candidate->photo) {
                    Storage::disk('public')->delete($candidate->photo);
                }
            }
            
            $candidate->update($updateData);

            return redirect()->route('candidates.view', $candidate->election_id)
                ->with('success', 'Candidate updated successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating candidate: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function view($id)
    {
        //
        $user = Auth::user();
        $election = Election::where('id', $id)
            ->where('tenant_id', $user->tenant_id)
            ->with(['candidates.portfolio'])
            ->firstOrFail();
        return view('tenant.candidates.view', compact('election'));
    }

    /**
     * Display individual candidate details (read-only).
     */
    public function details($id)
    {
        //
        $user = Auth::user();
        $candidate = Candidate::where('id', $id)
            ->whereHas('election', function ($query) use ($user) {
                $query->where('tenant_id', $user->tenant_id);
            })
            ->with(['election', 'portfolio'])
            ->firstOrFail();
        return view('tenant.candidates.details', compact('candidate'));
    }

    /**
     * Show the upload form for the specified election.
     */
    public function upload($id)
    {
        //
        $user = Auth::user();
        $election = Election::where('id', $id)
            ->where('tenant_id', $user->tenant_id)
            ->with('portfolios')
            ->firstOrFail();
        return view('tenant.candidates.upload', compact('election'));
    }

    /**
     * Handle file upload for the specified election.
     */
    public function uploadFile(Request $request, $id)
    {
        //
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:50240', // Max 50MB
        ]);

        try {
            $file = $request->file('excel_file');
            $candidates = $this->parseExcelFile($file);
            
            // Store candidates in session for next step
            session(['uploaded_candidates' => $candidates, 'election_id' => $id]);
            
            return redirect()->route('candidates.assign.portfolios', $id)
                ->with('success', 'File uploaded successfully! Please assign portfolios to candidates.');
                
        } catch (\Exception $e) {
            return redirect()->route('candidates.upload', $id)
                ->with('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    /**
     * Show portfolio assignment form for uploaded candidates.
     */
    public function assignPortfolios($id)
    {
        //
        if (!session('uploaded_candidates')) {
            return redirect()->route('candidates.upload', $id)
                ->with('error', 'No uploaded candidates found. Please upload a file first.');
        }

        $user = Auth::user();
        $election = Election::where('id', $id)
            ->where('tenant_id', $user->tenant_id)
            ->with('portfolios')
            ->firstOrFail();
        $candidates = session('uploaded_candidates');
        
        return view('tenant.candidates.assign-portfolios', compact('election', 'candidates'));
    }

    /**
     * Save candidates with assigned portfolios.
     */
    public function saveCandidatesWithPortfolios(Request $request, $id)
    {
        //
        $request->validate([
            'candidates' => 'required|array',
            'candidates.*.portfolio_id' => 'required|exists:portfolios,id',
        ]);

        try {
            $candidates = session('uploaded_candidates');
            $user = Auth::user();
            $election = Election::where('id', $id)
                ->where('tenant_id', $user->tenant_id)
                ->firstOrFail();
            
            foreach ($request->candidates as $index => $candidateData) {
                if (isset($candidates[$index])) {
                    $candidate = array_merge($candidates[$index], [
                        'portfolio_id' => $candidateData['portfolio_id'],
                        'election_id' => $election->id,
                        'tenant_id' => $election->tenant_id,
                        'profile_complete' => false,
                    ]);
                    
                    Candidate::create($candidate);
                }
            }
            
            // Clear session
            session()->forget(['uploaded_candidates', 'election_id']);
            
            return redirect()->route('candidates.view', $id)
                ->with('success', 'Candidates uploaded and portfolios assigned successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error saving candidates: ' . $e->getMessage());
        }
    }

    /**
     * Parse Excel file and return candidate data.
     */
    private function parseExcelFile($file)
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $candidates = [];
            
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
                
                // Map columns to candidate fields (adjust indices based on your Excel format)
                $candidate = [
                    'staff_number' => $rowData[0] ?? null,
                    'first_name' => $rowData[1] ?? null,
                    'last_name' => $rowData[2] ?? null,
                    'gender' => $rowData[3] ?? null,
                    'phone' => $rowData[4] ?? null,
                    'email' => $rowData[5] ?? null,
                ];
                
                // Validate required fields
                if (!empty($candidate['staff_number']) && !empty($candidate['first_name']) && !empty($candidate['last_name'])) {
                    $candidates[] = $candidate;
                }
            }
            
            if (empty($candidates)) {
                throw new \Exception('No valid candidate data found in the Excel file. Please check the file format.');
            }
            
            return $candidates;
            
        } catch (\Exception $e) {
            throw new \Exception('Error parsing Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $candidate = Candidate::where('id', $id)
                ->whereHas('election', function ($query) use ($user) {
                    $query->where('tenant_id', $user->tenant_id);
                })
                ->firstOrFail();
            $electionId = $candidate->election_id;
            $candidate->delete();
            
            return redirect()->route('candidates.view', $electionId)
                ->with('success', 'Candidate deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting candidate: ' . $e->getMessage());
        }
    }

    /**
     * Notify all candidates for an election via email and SMS.
     *
     * Flow: Validate Settings → Send to Each Candidate → Return Result
     */
    public function notify($electionId)
    {
        try {
            // 1. Load election with candidates
            $user = Auth::user();
            $election = Election::where('id', $electionId)
                ->where('tenant_id', $user->tenant_id)
                ->with('candidates')
                ->firstOrFail();
            $candidates = $election->candidates;

            if ($candidates->isEmpty()) {
                return $this->notificationResponse('No candidates found for this election.', 'warning');
            }

            // 2. Check notification settings
            $settings = $this->getNotificationSettings('candidate');
            if (!$settings['email_enabled'] && !$settings['sms_enabled']) {
                return $this->notificationResponse('Both email and SMS notifications are disabled in settings.', 'warning');
            }

            // 3. Send notifications to each candidate
            $smsService = new SMSService();
            $stats = ['email_sent' => 0, 'email_failed' => 0, 'sms_sent' => 0, 'sms_failed' => 0, 'no_email' => 0, 'processed' => 0];
            $processedIds = [];

            Log::info('Starting bulk candidate notification', [
                'election_id' => $electionId,
                'total_candidates' => $candidates->count(),
                'candidate_ids' => $candidates->pluck('id')->toArray(),
            ]);

            foreach ($candidates as $candidate) {
                $stats['processed']++;
                $processedIds[] = $candidate->id;

                Log::debug('Processing candidate', [
                    'candidate_id' => $candidate->id,
                    'email' => $candidate->email,
                    'phone' => $candidate->phone,
                    'progress' => $stats['processed'] . '/' . $candidates->count(),
                ]);

                $token = $this->generateCandidateToken($candidate);
                $editUrl = route('candidate.edit.profile', ['token' => $token]);
                $deadline = $candidate->profile_edit_deadline ?? now()->addDays(7)->format('F j, Y \a\t g:i A');

                // Send Email
                if ($settings['email_enabled'] && $candidate->email) {
                    $sent = $this->sendCandidateEmail($candidate, $election, $editUrl, $deadline);
                    $sent ? $stats['email_sent']++ : $stats['email_failed']++;
                    Log::debug('Email result', ['candidate_id' => $candidate->id, 'sent' => $sent]);
                } elseif ($settings['email_enabled']) {
                    $stats['no_email']++;
                    Log::debug('Skipped - no email', ['candidate_id' => $candidate->id]);
                }

                // Send SMS
                if ($settings['sms_enabled'] && $candidate->phone && $smsService->isConfigured()) {
                    $sent = $this->sendCandidateSMS($candidate, $election, $editUrl, $smsService);
                    $sent ? $stats['sms_sent']++ : $stats['sms_failed']++;
                }
            }

            Log::info('Completed bulk candidate notification', [
                'election_id' => $electionId,
                'processed_ids' => $processedIds,
                'stats' => $stats,
            ]);

            // 4. Build and return response
            $message = $this->buildNotificationMessage($stats, $candidates->count(), $settings);
            return $this->notificationResponse($message, 'success');

        } catch (\Exception $e) {
            Log::error('Bulk candidate notification failed', ['election_id' => $electionId, 'error' => $e->getMessage()]);
            return $this->notificationResponse('Failed to send notifications: ' . $e->getMessage(), 'error', 500);
        }
    }

    /**
     * Notify a single candidate via email and SMS.
     */
    public function notifySingle($candidateId)
    {
        try {
            // 1. Load candidate with election
            $user = Auth::user();
            $candidate = Candidate::where('id', $candidateId)
                ->whereHas('election', function ($query) use ($user) {
                    $query->where('tenant_id', $user->tenant_id);
                })
                ->with('election')
                ->firstOrFail();
            $election = $candidate->election;

            Log::info('Starting single candidate notification', [
                'candidate_id' => $candidateId,
                'candidate_name' => $candidate->first_name . ' ' . $candidate->last_name,
                'email' => $candidate->email,
                'phone' => $candidate->phone,
            ]);

            // 2. Check notification settings
            $settings = $this->getNotificationSettings('candidate');
            if (!$settings['email_enabled'] && !$settings['sms_enabled']) {
                return redirect()->back()->with('warning', 'Both email and SMS notifications are disabled in settings.');
            }

            // 3. Prepare notification data
            $token = $this->generateCandidateToken($candidate);
            $editUrl = route('candidate.edit.profile', ['token' => $token]);
            $deadline = $candidate->profile_edit_deadline ?? now()->addDays(7)->format('F j, Y \a\t g:i A');
            $smsService = new SMSService();

            // 4. Send notifications
            $results = [];

            if ($settings['email_enabled']) {
                if ($candidate->email) {
                    $sent = $this->sendCandidateEmail($candidate, $election, $editUrl, $deadline);
                    $results[] = $sent ? 'email sent' : 'email failed';
                    Log::info('Single candidate email result', ['candidate_id' => $candidateId, 'sent' => $sent]);
                } else {
                    $results[] = 'email skipped (no email)';
                    Log::info('Single candidate has no email', ['candidate_id' => $candidateId]);
                }
            }

            if ($settings['sms_enabled'] && $candidate->phone && $smsService->isConfigured()) {
                $sent = $this->sendCandidateSMS($candidate, $election, $editUrl, $smsService);
                $results[] = $sent ? 'SMS sent' : 'SMS failed';
            }

            // 5. Return response
            $name = $candidate->first_name . ' ' . $candidate->last_name;
            $message = "Notification to {$name}: " . implode(', ', $results) . '.';

            Log::info('Completed single candidate notification', ['candidate_id' => $candidateId, 'message' => $message]);

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Single candidate notification failed', ['candidate_id' => $candidateId, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to send notification: ' . $e->getMessage());
        }
    }

    /**
     * Get notification settings for a recipient type (candidate or voter).
     */
    private function getNotificationSettings(string $type): array
    {
        $tenant = auth()->user()->tenant;
        return [
            'email_enabled' => $tenant->{"enable_{$type}_email_notifications"} ?? false,
            'sms_enabled' => $tenant->{"enable_{$type}_sms_notifications"} ?? false,
            'mail_configured' => config('mail.default') !== 'log',
        ];
    }

    /**
     * Generate and save a 5-character token for a candidate.
     */
    private function generateCandidateToken($candidate): string
    {
        $token = \Illuminate\Support\Str::random(5);
        $candidate->edit_token_hash = hash('sha256', $token);
        $candidate->save();
        return $token;
    }

    /**
     * Build a human-readable notification summary message.
     */
    private function buildNotificationMessage(array $stats, int $total, array $settings): string
    {
        $parts = [];

        // Always show processed count
        $parts[] = "{$stats['processed']}/{$total} candidates processed";

        if ($settings['email_enabled']) {
            $parts[] = "{$stats['email_sent']} emails sent";
            if ($stats['email_failed'] > 0) {
                $parts[] = "{$stats['email_failed']} emails failed";
            }
            if ($stats['no_email'] > 0) {
                $parts[] = "{$stats['no_email']} no email address";
            }
        }

        if ($settings['sms_enabled']) {
            $parts[] = "{$stats['sms_sent']} SMS sent";
            if ($stats['sms_failed'] > 0) {
                $parts[] = "{$stats['sms_failed']} SMS failed";
            }
        }

        $message = implode(', ', $parts);

        if ($settings['email_enabled'] && !$settings['mail_configured'] && $stats['email_sent'] === 0) {
            $message .= ' (Check MAIL_MAILER in .env)';
        }

        return $message;
    }

    /**
     * Return notification response (JSON for AJAX, redirect for regular requests).
     */
    private function notificationResponse(string $message, string $type = 'success', int $httpCode = 200)
    {
        if (request()->ajax() || request()->wantsJson()) {
            $success = in_array($type, ['success']);
            return response()->json(['success' => $success, 'message' => $message], $httpCode > 200 ? $httpCode : ($success ? 200 : 400));
        }

        return redirect()->back()->with($type, $message);
    }

    /**
     * Send email notification to candidate.
     */
    private function sendCandidateEmail($candidate, $election, $editUrl, $deadline): bool
    {
        try {
            // Log email attempt
            $emailLog = EmailLog::create([
                'recipient_type' => 'candidate',
                'recipient_id' => $candidate->id,
                'recipient_email' => $candidate->email,
                'recipient_name' => $candidate->first_name . ' ' . $candidate->last_name,
                'email_type' => 'profile_update',
                'subject' => 'Action Required: Update Your Candidate Profile',
                'election_id' => $election->id,
                'status' => 'pending',
                'ip_address' => request()->ip(),
            ]);

            // Send email
            Mail::send('emails.candidate-profile-update', [
                'candidate' => $candidate,
                'editUrl' => $editUrl,
                'deadline' => $deadline,
            ], function ($message) use ($candidate) {
                $message->to($candidate->email)
                    ->subject('Action Required: Update Your Candidate Profile');
            });

            $emailLog->update(['status' => 'sent', 'sent_at' => now()]);
            return true;

        } catch (\Exception $e) {
            Log::error('Email send failed', ['candidate_id' => $candidate->id, 'error' => $e->getMessage()]);
            if (isset($emailLog)) {
                $emailLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            }
            return false;
        }
    }

    /**
     * Send SMS notification to candidate.
     */
    private function sendCandidateSMS($candidate, $election, $editUrl, SMSService $smsService): bool
    {
        try {
            $message = sprintf("Hi %s, update your profile: %s", $candidate->first_name, $editUrl);

            // Log SMS attempt
            $smsLog = SmsLog::create([
                'recipient_type' => 'candidate',
                'recipient_id' => $candidate->id,
                'recipient_phone' => $candidate->phone,
                'recipient_name' => $candidate->first_name . ' ' . $candidate->last_name,
                'sms_type' => 'profile_update',
                'message' => $message,
                'election_id' => $election->id,
                'status' => 'pending',
                'ip_address' => request()->ip(),
            ]);

            // Send SMS
            $result = $smsService->send($candidate->phone, $message);

            $success = $result['success'] ?? false;
            $smsLog->update([
                'status' => $success ? 'sent' : 'failed',
                'sent_at' => $success ? now() : null,
                'error_message' => $result['error'] ?? null,
            ]);

            return $success;

        } catch (\Exception $e) {
            Log::error('SMS send failed', ['candidate_id' => $candidate->id, 'error' => $e->getMessage()]);
            if (isset($smsLog)) {
                $smsLog->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            }
            return false;
        }
    }

    /**
     * Show candidate profile edit form (public access via token)
     */
    public function editProfile($token)
    {
        $tokenHash = hash('sha256', $token);
        $candidate = Candidate::where('edit_token_hash', $tokenHash)
            ->with(['election', 'portfolio'])
            ->first();

        if (!$candidate) {
            return view('public.candidate-edit', [
                'error' => 'Invalid or expired link. Please contact the election administrator.',
            ]);
        }

        // Check if election status allows profile editing (before election starts)
        $election = $candidate->election;
        if (!$election || !$election->canEditProfiles()) {
            return view('public.candidate-edit', [
                'error' => 'Profile editing is not available. This feature is only available before the election starts (Draft or Pending status).',
                'candidate' => $candidate,
            ]);
        }

        // Check if deadline has passed
        if ($candidate->profile_edit_deadline && now()->gt($candidate->profile_edit_deadline)) {
            return view('public.candidate-edit', [
                'error' => 'The profile editing period has expired. Please contact the election administrator.',
                'candidate' => $candidate,
            ]);
        }

        // Check if profile is already complete
        if ($candidate->profile_complete) {
            return view('public.candidate-edit', [
                'error' => 'Your profile has already been submitted and cannot be edited further.',
                'candidate' => $candidate,
            ]);
        }

        // Get all portfolios for this election
        $portfolios = Portfolio::where('election_id', $candidate->election_id)->get();

        return view('public.candidate-edit', [
            'candidate' => $candidate,
            'election' => $candidate->election,
            'portfolios' => $portfolios,
            'token' => $token,
        ]);
    }

    /**
     * Update candidate profile (public access via token)
     */
    public function updateProfile(Request $request, $token)
    {
        $tokenHash = hash('sha256', $token);
        $candidate = Candidate::where('edit_token_hash', $tokenHash)->first();

        if (!$candidate) {
            return redirect()->back()
                ->with('error', 'Invalid or expired link.');
        }

        // Check if election status allows profile editing (before election starts)
        $election = $candidate->election;
        if (!$election || !$election->canEditProfiles()) {
            return redirect()->back()
                ->with('error', 'Profile editing is not available. This feature is only available before the election starts (Draft or Pending status).');
        }

        // Check deadline
        if ($candidate->profile_edit_deadline && now()->gt($candidate->profile_edit_deadline)) {
            return redirect()->back()
                ->with('error', 'The profile editing period has expired.');
        }

        // Check if already submitted
        if ($candidate->profile_complete) {
            return redirect()->back()
                ->with('error', 'Your profile has already been submitted and cannot be edited further.');
        }

        // Validate
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'manifesto' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'email' => $request->email,
                'phone' => $request->phone,
                'job_title' => $request->job_title,
                'manifesto' => $request->manifesto,
                'profile_complete' => true,
            ];

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = time() . '_' . $candidate->staff_number . '.' . $photo->getClientOriginalExtension();
                $photoPath = $photo->storeAs('candidates/photos', $photoName, 'public');
                $updateData['photo'] = $photoPath;

                // Delete old photo if exists
                if ($candidate->photo) {
                    Storage::disk('public')->delete($candidate->photo);
                }
            }

            $candidate->update($updateData);

            // Invalidate the token
            $candidate->update(['edit_token_hash' => null]);

            return view('public.candidate-edit', [
                'success' => 'Your profile has been updated successfully. Thank you!',
                'candidate' => $candidate,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}
