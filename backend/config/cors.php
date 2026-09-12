<?php

// Only relevant to the /api/* Sanctum surface — the Blade admin/portal/
// public UI is served same-origin and never triggers CORS at all. This API
// remains available for any external consumer (a mobile app, another
// site, etc), so allowed_origins stays configurable rather than removed
// outright now that the original Next.js frontend is gone.
//
// Auth is pure Sanctum Bearer-token (no cookie/session-based SPA auth), so
// supports_credentials stays false — nothing here relies on the browser
// sending cookies cross-origin.
return [

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter(explode(',', (string) env(
        'CORS_ALLOWED_ORIGINS',
        'http://tramax.local'
    )))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
