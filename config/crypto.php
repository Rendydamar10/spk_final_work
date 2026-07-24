<?php

return [
    'coingecko' => [
         'plan' => env('COINGECKO_PLAN', 'demo'),
        'base_url' => env('COINGECKO_BASE_URL', 'https://api.coingecko.com/api/v3'),
        'api_key' => env('COINGECKO_API_KEY'),
        'timeout_seconds' => env('COINGECKO_TIMEOUT_SECONDS', 20),
    ],

    'cache' => [
        'market_chart_ttl_seconds' => env('CRYPTO_MARKET_CHART_CACHE_TTL', 1800),
    ],

    'currency' => env('CRYPTO_VS_CURRENCY', 'usd'),
];