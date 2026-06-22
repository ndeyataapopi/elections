<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Billing Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | Define the pricing for various billable services in the election system.
    | Prices are in USD by default.
    |
    */

    'pricing' => [
        // Cost per email sent
        'email' => 0.001, // $0.001 per email

        // Cost per SMS sent
        'sms' => 0.05, // $0.05 per SMS

        // Monthly base cost per tenant
        'base_monthly' => 10.00,
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing Settings
    |--------------------------------------------------------------------------
    |
    | Various billing configuration settings.
    |
    */

    'settings' => [
        // Currency code
        'currency' => 'USD',

        // Currency symbol
        'currency_symbol' => '$',

        // Days until invoice is considered overdue
        'overdue_days' => 30,

        // Auto-generate billings monthly
        'auto_generate' => false,

        // Payment methods available
        'payment_methods' => [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'mobile_money' => 'Mobile Money',
            'credit_card' => 'Credit Card',
            'other' => 'Other',
        ],
    ],
];
