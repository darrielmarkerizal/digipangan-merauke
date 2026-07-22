<?php

return [
    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrator'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    /*
     * Base URL of the public-facing frontend, used to build canonical URLs,
     * sitemap entries, and Open Graph links. Falls back to APP_URL when the
     * frontend is served from the same origin.
     */
    'frontend_url' => env('FRONTEND_URL', env('APP_URL', 'http://localhost')),
];
