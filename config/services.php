<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'sms' => [
        'key' => env('SMS_KEY'),
        'base_url' => env('SMS_URL', 'https://smspoh.com/api'),
        'verify_url' => env('SMS_VERIFY_LINK', 'https://verify.smspoh.com/api'),
        'short_url' => env('SMS_SHORTURL_LINK', '/url-shortener/links'),
        'send_api' => env('SMS_SEND_ENDPOINT', '/v2/send'),
        'verify_request' => env('SMS_REQUEST_ENDPOINT', '/v1/request'),
        'verify' => env('SMS_VERIFY_ENDPOINT', '/v1/verify'),
    ],

    'shareable_link' => [
        'dynamic_domain' => env('DYNAMIC_DOMAIN'),
        'deep_link' => env('DEEP_LINK')
    ],
    'firebase_services' => [
        'realtime_database_url' => env('Firebase_Realtime_DB_Url')
    ]

];
