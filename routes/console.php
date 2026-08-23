<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Requires cron calling `php artisan schedule:run` every minute — see DEPLOY_SHARED_HOSTING.md.
Schedule::command('orders:remind-pending')->hourly();
