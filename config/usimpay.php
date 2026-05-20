<?php

return [
    /*
    |--------------------------------------------------------------------------
    | USIMPay API
    |--------------------------------------------------------------------------
    */

    'base_url' => env('USIMPAY_BASE_URL', 'https://api.usimpay.com.my'),

    'api_key' => env('USIMPAY_API_KEY'),

    'secret_key' => env('USIMPAY_SECRET_KEY'),

    'timeout' => (int) env('USIMPAY_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Payment Payload
    |--------------------------------------------------------------------------
    |
    | USIMPay descriptions may have gateway-side length constraints. The
    | client truncates descriptions to this length before sending requests.
    |
    */

    'description_max_length' => (int) env('USIMPAY_DESCRIPTION_MAX_LENGTH', 20),

    /*
    |--------------------------------------------------------------------------
    | Callback Security
    |--------------------------------------------------------------------------
    */

    'verify_callbacks' => (bool) env('USIMPAY_VERIFY_CALLBACKS', true),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Null uses Laravel's default log channel.
    |
    */

    'log_channel' => env('USIMPAY_LOG_CHANNEL'),
];
