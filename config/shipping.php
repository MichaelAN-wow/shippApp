<?php

return [
    'ups' => [
        'client_id'       => env('UPS_CLIENT_ID'),
        'client_secret'   => env('UPS_CLIENT_SECRET'),
        'access_key'      => env('UPS_ACCESS_KEY'),
        'account_number'  => env('UPS_ACCOUNT_NUMBER'),
        'base_url'        => env('UPS_BASE_URL', 'https://wwwcie.ups.com'),
    ],
];
