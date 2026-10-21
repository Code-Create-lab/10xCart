<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Queue worker for shared hosting (no Supervisor).
| Server cron runs `php artisan schedule:run` every minute; this then
| processes queued jobs (e.g. contact-form emails) and exits before the
| next run. withoutOverlapping() stops two workers running at once.
*/
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->runInBackground();
