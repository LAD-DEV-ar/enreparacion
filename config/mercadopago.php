<?php

return [

    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),

    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),

    'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),

    'environment' => env('MERCADOPAGO_ENV', 'test'),

    'currency' => env('MERCADOPAGO_CURRENCY', 'ARS'),

    'frequency' => env('MERCADOPAGO_FREQUENCY', 1),

    'frequency_type' => env('MERCADOPAGO_FREQUENCY_TYPE', 'months'),

    'repetitions' => env('MERCADOPAGO_REPETITIONS', 12),

    'trial_days' => env('SUSCRIPCION_TRIAL_DAYS', 30),

    'back_url' => env('APP_URL', 'http://localhost:8000'),
];
