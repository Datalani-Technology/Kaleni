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

    'dpo' => [
        // Filled in once DPO Group approves the merchant account. Until then
        // this stays empty and the storefront keeps the DPO option disabled.
        // Guards against leftover placeholder values (e.g. a stale .env still
        // carrying the old hardcoded default) being mistaken for a real token.
        'company_token' => in_array(env('DPO_COMPANY_TOKEN'), [null, '', 'your-company-token', 'your-company-token-here'], true)
            ? null
            : env('DPO_COMPANY_TOKEN'),
        'service_type' => env('DPO_SERVICE_TYPE', '1'),
        'test_mode' => env('DPO_TEST_MODE', true),
        'currency' => env('DPO_CURRENCY', 'NAD'),
        'api_url' => env('DPO_API_URL', 'https://secure.3gdirectpay.com/API/v6/'),
        'pay_url' => env('DPO_PAY_URL', 'https://secure.3gdirectpay.com/payv2.php'),
    ],

];
