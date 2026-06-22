<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService
{
    protected string $provider;
    protected array $config;

    public function __construct()
    {
        $this->provider = config('services.sms.provider', 'twilio');
        $this->config = config('services.sms.' . $this->provider, []);
    }

    /**
     * Send SMS message
     *
     * @param string $to Phone number to send to
     * @param string $message SMS message content
     * @return array ['success' => bool, 'response' => mixed, 'error' => string|null]
     */
    public function send(string $to, string $message): array
    {
        try {
            // Normalize phone number (unless disabled in config)
            $normalize = $this->config['normalize_numbers'] ?? true;
            if ($normalize) {
                $to = $this->normalizePhoneNumber($to);
            }

            return match ($this->provider) {
                'twilio' => $this->sendViaTwilio($to, $message),
                'africastalking' => $this->sendViaAfricaTalking($to, $message),
                'vonage' => $this->sendViaVonage($to, $message),
                'custom' => $this->sendViaCustom($to, $message),
                default => ['success' => false, 'response' => null, 'error' => 'Unknown SMS provider: ' . $this->provider],
            };
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage(), [
                'to' => $to,
                'provider' => $this->provider,
            ]);

            return [
                'success' => false,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio(string $to, string $message): array
    {
        $accountSid = $this->config['account_sid'] ?? '';
        $authToken = $this->config['auth_token'] ?? '';
        $fromNumber = $this->config['from_number'] ?? '';

        if (empty($accountSid) || empty($authToken) || empty($fromNumber)) {
            return [
                'success' => false,
                'response' => null,
                'error' => 'Twilio credentials not configured',
            ];
        }

        Log::debug('SMS: Sending via Twilio', [
            'to' => $to,
            'from' => $fromNumber,
            'message_length' => strlen($message),
        ]);

        $response = Http::withBasicAuth($accountSid, $authToken)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                'From' => $fromNumber,
                'To' => $to,
                'Body' => $message,
            ]);

        $responseData = $response->json();
        Log::debug('SMS: Twilio response', [
            'status' => $response->status(),
            'response' => $responseData,
        ]);

        if ($response->successful()) {
            // Check if message was actually accepted (status: queued, sent, or delivered)
            $messageStatus = $responseData['status'] ?? 'unknown';
            Log::debug('SMS: Twilio message status', ['status' => $messageStatus]);

            return [
                'success' => true,
                'response' => $responseData,
                'error' => null,
            ];
        }

        $errorMsg = $responseData['message'] ?? $responseData['error_message'] ?? 'Unknown error';
        Log::error('SMS: Twilio error', ['error' => $errorMsg, 'response' => $responseData]);

        return [
            'success' => false,
            'response' => $responseData,
            'error' => $errorMsg,
        ];
    }

    /**
     * Send SMS via Africa's Talking
     */
    protected function sendViaAfricaTalking(string $to, string $message): array
    {
        $username = $this->config['username'] ?? '';
        $apiKey = $this->config['api_key'] ?? '';
        $from = $this->config['from'] ?? '';

        if (empty($username) || empty($apiKey)) {
            return [
                'success' => false,
                'response' => null,
                'error' => 'Africa\'s Talking credentials not configured',
            ];
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'apiKey' => $apiKey,
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            'to' => $to,
            'message' => $message,
            'from' => $from,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $success = isset($data['SMSMessageData']['Recipients']) &&
                       count($data['SMSMessageData']['Recipients']) > 0 &&
                       $data['SMSMessageData']['Recipients'][0]['status'] === 'Success';

            return [
                'success' => $success,
                'response' => $data,
                'error' => $success ? null : ($data['SMSMessageData']['Recipients'][0]['status'] ?? 'Unknown error'),
            ];
        }

        return [
            'success' => false,
            'response' => $response->json(),
            'error' => $response->body(),
        ];
    }

    /**
     * Send SMS via Vonage (formerly Nexmo)
     */
    protected function sendViaVonage(string $to, string $message): array
    {
        $apiKey = $this->config['api_key'] ?? '';
        $apiSecret = $this->config['api_secret'] ?? '';
        $from = $this->config['from'] ?? 'ElectionApp';

        if (empty($apiKey) || empty($apiSecret)) {
            return [
                'success' => false,
                'response' => null,
                'error' => 'Vonage credentials not configured',
            ];
        }

        $response = Http::withBasicAuth($apiKey, $apiSecret)
            ->post('https://rest.nexmo.com/sms/json', [
                'from' => $from,
                'to' => $to,
                'text' => $message,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $success = isset($data['messages'][0]['status']) && $data['messages'][0]['status'] === '0';

            return [
                'success' => $success,
                'response' => $data,
                'error' => $success ? null : ($data['messages'][0]['error-text'] ?? 'Unknown error'),
            ];
        }

        return [
            'success' => false,
            'response' => $response->json(),
            'error' => 'HTTP request failed',
        ];
    }

    /**
     * Send SMS via custom webhook/API
     */
    protected function sendViaCustom(string $to, string $message): array
    {
        $endpoint = $this->config['endpoint'] ?? '';
        $apiKey = $this->config['api_key'] ?? '';
        $headers = $this->config['headers'] ?? [];
        $payloadFormat = $this->config['payload_format'] ?? 'json';

        if (empty($endpoint)) {
            return [
                'success' => false,
                'response' => null,
                'error' => 'Custom SMS endpoint not configured',
            ];
        }

        $request = Http::withHeaders($headers);

        if (!empty($apiKey)) {
            $request = $request->withToken($apiKey);
        }

        if ($payloadFormat === 'form') {
            $response = $request->asForm()->post($endpoint, [
                'to' => $to,
                'message' => $message,
            ]);
        } else {
            $response = $request->post($endpoint, [
                'to' => $to,
                'message' => $message,
            ]);
        }

        if ($response->successful()) {
            return [
                'success' => true,
                'response' => $response->json(),
                'error' => null,
            ];
        }

        return [
            'success' => false,
            'response' => $response->json(),
            'error' => $response->body(),
        ];
    }

    /**
     * Normalize phone number to international format
     */
    protected function normalizePhoneNumber(string $phone): string
    {
        // Remove all non-digit characters except leading + or 00
        $phone = preg_replace('/[^\d+]/', '', $phone);

        Log::debug('SMS: Normalizing phone', ['original' => $phone]);

        // Already has + prefix - keep as-is (e.g., +264814686622)
        if (str_starts_with($phone, '+')) {
            Log::debug('SMS: Phone already has + prefix, keeping as-is', ['normalized' => $phone]);
            return $phone;
        }

        // Has 00 prefix - convert to + (e.g., 00264... → +264...)
        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
            Log::debug('SMS: Converted 00 prefix to +', ['normalized' => $phone]);
            return $phone;
        }

        $defaultCountryCode = $this->config['default_country_code'] ?? '264'; // Default to Namibia

        // Check if number already starts with country code (e.g., 264814686622 → +264814686622)
        if (str_starts_with($phone, $defaultCountryCode)) {
            $phone = '+' . $phone;
            Log::debug('SMS: Added + prefix to number with country code', ['normalized' => $phone]);
            return $phone;
        }

        // Local number (e.g., 0814686622) - remove leading 0 and add country code
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        $phone = '+' . $defaultCountryCode . $phone;
        Log::debug('SMS: Added country code to local number', ['normalized' => $phone]);

        return $phone;
    }

    /**
     * Check if SMS service is configured
     */
    public function isConfigured(): bool
    {
        return match ($this->provider) {
            'twilio' => !empty($this->config['account_sid']) && !empty($this->config['auth_token']),
            'africastalking' => !empty($this->config['username']) && !empty($this->config['api_key']),
            'vonage' => !empty($this->config['api_key']) && !empty($this->config['api_secret']),
            'custom' => !empty($this->config['endpoint']),
            default => false,
        };
    }
}
