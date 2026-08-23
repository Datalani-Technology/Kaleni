<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel Path
    |--------------------------------------------------------------------------
    |
    | The URL prefix the admin panel is served under. Keep this out of version
    | control (set it in .env only) and out of any public file such as
    | robots.txt, so the panel's location isn't discoverable.
    |
    */
    'path' => env('ADMIN_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Idle Session Timeout
    |--------------------------------------------------------------------------
    |
    | Minutes of inactivity before an authenticated admin/editor is force
    | logged out, independent of the overall session lifetime. Set to 0 to
    | disable. Keep this short — the panel handles orders, payments and
    | customer data.
    |
    */
    'idle_timeout_minutes' => (int) env('ADMIN_IDLE_TIMEOUT_MINUTES', 20),
];
