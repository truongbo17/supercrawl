<?php

return [
    "browsers" => [
        "guzzle" => [
            'timeout' => env('BROWSER_TIMEOUT', 1000),
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            ],
            'http_errors' => false,
            'allow_redirects' => [
                'track_redirects' => true
            ],
        ],
    ],
    'should_retry_status_codes' => [
        408, // Request Timeout
        429, // Too Many Requests
        509, // Bandwidth Limit Exceeded (Apache)
    ],
];
