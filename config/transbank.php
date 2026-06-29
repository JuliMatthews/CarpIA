<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Transbank Environment
    |--------------------------------------------------------------------------
    |
    | Set the environment for Transbank. Use 'integration' for testing
    | and 'production' for live transactions.
    |
    | Supported: 'integration', 'production'
    |
    */

    'environment' => env('TRANSBANK_ENV', 'integration'),

    /*
    |--------------------------------------------------------------------------
    | Webpay Plus Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Webpay Plus credentials here. These are obtained
    | from Transbank after registering as a merchant.
    |
    */

    'webpay' => [
        'api_key' => env('WEBPAY_KEY'),
        'commerce_code' => env('WEBPAY_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Return URLs
    |--------------------------------------------------------------------------
    |
    | These URLs are used for redirecting the user back after payment.
    | Make sure to configure these in your Transbank portal as well.
    |
    */

    'return_url' => env('WEBPAY_RETURN_URL', '/checkout/return'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Timeout for payment sessions in minutes. Webpay allows 10 minutes
    | in integration and 4 minutes in production.
    |
    */

    'session_lifetime' => env('WEBPAY_SESSION_LIFETIME', 10),
];
