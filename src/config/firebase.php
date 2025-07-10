<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Firebase settings for the application.
    |
    */

    'credentials_path' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account-key.json')),

    'project_id' => env('FIREBASE_PROJECT_ID'),

    'database_url' => env('FIREBASE_DATABASE_URL'),
];
