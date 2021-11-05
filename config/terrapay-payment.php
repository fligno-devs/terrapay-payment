<?php

return [
    'sandbox' => [
        'api_url' => env('TERRAPAY_SANDBOX_API_URL'),
        'api_key' => env('TERRAPAY_SANDBOX_API_KEY'),
    ],
    'production' => [
        'api_url' => env('TERRAPAY_PRODUCTION_API_URL'),
        'api_key' => env('TERRAPAY_PRODUCTION_API_KEY'),
    ],
    'verify_ssl' => env('TERRAPAY_VERIFY_SSL', true),
    'environment' => env('TERRAPAY_ENVIRONMENT', 'sandbox')
];
