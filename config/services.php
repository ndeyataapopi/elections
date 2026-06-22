<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | Supported providers: twilio, africastalking, vonage, custom
    |
    */
    'sms' => [
        'provider' => env('SMS_PROVIDER', 'twilio'),
        'default_country_code' => env('SMS_DEFAULT_COUNTRY_CODE', '264'), // Namibia
        'normalize_numbers' => env('SMS_NORMALIZE_NUMBERS', true), // Set to false if all numbers already have + prefix

        'twilio' => [
            'account_sid' => env('TWILIO_ACCOUNT_SID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'from_number' => env('TWILIO_FROM_NUMBER'),
        ],

        'africastalking' => [
            'username' => env('AT_USERNAME'),
            'api_key' => env('AT_API_KEY'),
            'from' => env('AT_FROM'),
        ],

        'vonage' => [
            'api_key' => env('VONAGE_API_KEY'),
            'api_secret' => env('VONAGE_API_SECRET'),
            'from' => env('VONAGE_FROM', 'ElectionApp'),
        ],

        'custom' => [
            'endpoint' => env('SMS_CUSTOM_ENDPOINT'),
            'api_key' => env('SMS_CUSTOM_API_KEY'),
            'headers' => [],
            'payload_format' => env('SMS_CUSTOM_FORMAT', 'json'), // json or form
        ],
    ],

];
