<?php

return [
    'chzzk' => [
        'auth_url' => env('CHZZK_AUTH_URL', 'https://chzzk.naver.com/oauth/authorize'),
        'token_url' => env('CHZZK_TOKEN_URL', 'https://chzzk.naver.com/oauth/token'),
        'api_url' => env('CHZZK_API_URL', 'https://api.chzzk.naver.com'),
        'client_id' => env('CHZZK_CLIENT_ID'),
        'client_secret' => env('CHZZK_CLIENT_SECRET'),
    ],
    'youtube' => [
        'client_id' => env('YOUTUBE_CLIENT_ID'),
        'client_secret' => env('YOUTUBE_CLIENT_SECRET'),
        'api_key' => env('YOUTUBE_API_KEY'),
        'api_url' => env('YOUTUBE_API_URL', 'https://www.googleapis.com/youtube/v3'),
    ],
    'naver_commerce' => [
        'client_id' => env('NAVER_COMMERCE_CLIENT_ID'),
        'client_secret' => env('NAVER_COMMERCE_CLIENT_SECRET'),
        'base_url' => env('NAVER_COMMERCE_BASE_URL', 'https://api.commerce.naver.com'),
    ],
];
