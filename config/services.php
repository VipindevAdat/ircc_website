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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'givebutter' => [
        'api_key' => env('GIVEBUTTER_API_KEY'),
        // Full URL for CLI/testing (e.g. https://ircc.world/payment-response) when APP_URL is not public
        'webhook_callback_url' => env('GIVEBUTTER_WEBHOOK_CALLBACK_URL'),
        'webhook_secret' => env('GIVEBUTTER_WEBHOOK_SECRET'),
        'campaign_code' => env('GIVEBUTTER_CAMPAIGN_CODE'), // Required: Campaign code from Givebutter dashboard
        'campaign_title' => env('GIVEBUTTER_CAMPAIGN_TITLE'), // Alternative to campaign_code
        'campaign_id' => env('GIVEBUTTER_CAMPAIGN_ID'), // Legacy support
        'exchange_rate' => env('INR_TO_USD_RATE', 0.012), // Default: 1 INR = 0.012 USD (approx 83 INR = 1 USD)
    ],

];
