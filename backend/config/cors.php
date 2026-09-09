<?php

// No config/cors.php existed before this — Laravel's HandleCors middleware
// (registered globally by the framework) was silently falling back to its
// own bundled default of allowed_origins => ['*'], which is how cross-
// origin requests between the dev frontend (localhost:3000) and backend
// (tramax.local) "just worked" without anyone configuring it. That's fine
// for local dev but too permissive once a real staging/production frontend
// domain exists, so this pins allowed_origins explicitly instead.
//
// Auth is pure Sanctum Bearer-token (no cookie/session-based SPA auth), so
// supports_credentials stays false — nothing here relies on the browser
// sending cookies cross-origin.
return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter(explode(',', (string) env(
        'CORS_ALLOWED_ORIGINS',
        'http://localhost:3000,http://tramax.local'
    )))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
