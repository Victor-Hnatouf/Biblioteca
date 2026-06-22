<?php
return [
    'api_key' => env('GOOGLE_BOOKS_API_KEY'),
    'base_url' => 'https://www.googleapis.com/books/v1',
    'max_results' => (int) env('GOOGLE_BOOKS_MAX_RESULTS', 12),
    'timeout' => (int) env('GOOGLE_BOOKS_TIMEOUT', 15),
];
