<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\SMSService;
use App\Models\EmailLog;
use App\Models\SmsLog;

class TenantNotificationSettingsController extends Controller
{
    /**
     * Display notification settings page
     */
    public function index()
    {
        $tenant = tenant();
        return view('tenant.notifications.settings', compact('tenant'));
    }

    /**
     * Update notification settings
     */
    public function update(Request $request)
    {
        $tenant = tenant();

        $validated = $request->validate([
            'enable_candidate_email_notifications' => 'boolean',
            'enable_voter_email_notifications' => 'boolean',
            'enable_candidate_sms_notifications' => 'boolean',
            'enable_voter_sms_notifications' => 'boolean',
            'candidate_email_template' => 'nullable|string',
            'candidate_sms_template' => 'nullable|string',
            'voter_email_template' => 'nullable|string',
            'voter_sms_template' => 'nullable|string',
        ]);

        // Convert checkboxes to boolean (check actual value, not just presence)
        $validated['enable_candidate_email_notifications'] = $request->input('enable_candidate_email_notifications') == '1';
        $validated['enable_voter_email_notifications'] = $request->input('enable_voter_email_notifications') == '1';
        $validated['enable_candidate_sms_notifications'] = $request->input('enable_candidate_sms_notifications') == '1';
        $validated['enable_voter_sms_notifications'] = $request->input('enable_voter_sms_notifications') == '1';

        $tenant->update($validated);

        return redirect()->back()->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Send test email
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $tenant = tenant();
            $testEmail = $request->input('test_email');

            // Create email log
            $emailLog = EmailLog::create([
                'recipient_type' => 'test',
                'recipient_id' => 0, // Test emails don't have a real recipient
                'recipient_email' => $testEmail,
                'recipient_name' => 'Test User',
                'email_type' => 'test',
                'subject' => 'Test Email from ' . $tenant->name,
                'status' => 'pending',
                'ip_address' => request()->ip(),
            ]);

            // Send styled HTML test email
            Mail::send('emails.test-email', [
                'tenant' => $tenant,
                'recipientEmail' => $testEmail,
            ], function ($message) use ($testEmail, $tenant) {
                $message->to($testEmail)
                    ->subject('✅ Test Email - ' . $tenant->name . ' Email System');
            });

            // Update log
            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Test email sent successfully to ' . $testEmail);
        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Send test SMS
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string',
        ]);

        try {
            $tenant = tenant();
            $testPhone = $request->input('test_phone');
            $smsService = new SMSService();

            if (!$smsService->isConfigured()) {
                return redirect()->back()->with('error', 'SMS service is not configured. Please check your Twilio settings.');
            }

            $message = "Test SMS from {$tenant->name} election system. If you received this, SMS notifications are working correctly.";

            // Create SMS log
            $smsLog = SmsLog::create([
                'recipient_type' => 'test',
                'recipient_id' => 0, // Test SMS don't have a real recipient
                'recipient_phone' => $testPhone,
                'recipient_name' => 'Test User',
                'sms_type' => 'test',
                'message' => $message,
                'status' => 'pending',
                'ip_address' => request()->ip(),
            ]);

            // Send test SMS
            $result = $smsService->send($testPhone, $message);

            // Update log
            $smsLog->update([
                'status' => $result['success'] ? 'sent' : 'failed',
                'sent_at' => $result['success'] ? now() : null,
                'provider_response' => json_encode($result['response']),
                'error_message' => $result['error'],
            ]);

            if ($result['success']) {
                return redirect()->back()->with('success', 'Test SMS sent successfully to ' . $testPhone);
            } else {
                return redirect()->back()->with('error', 'Failed to send test SMS: ' . $result['error']);
            }
        } catch (\Exception $e) {
            Log::error('Test SMS failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send test SMS: ' . $e->getMessage());
        }
    }
}
