<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes & Scheduler Definitions (Sections 62 & 63)
|--------------------------------------------------------------------------
|
| Scheduled maintenance tasks:
| 1. Delete expired temporary files
| 2. Aggregate analytics
| 3. Generate scheduled reports
| 4. Subscription checks
| 5. Cleanup old data
| 6. Send scheduled emails
|
*/

// Daily cleanup of expired temporary files (exports, zips, temp uploads)
Schedule::command('clean:temp-files')->daily()->withoutOverlapping();

// Daily analytics aggregation at 00:05
Schedule::command('analytics:aggregate')->dailyAt('00:05')->withoutOverlapping();

// Daily scheduled report compilation at 01:00
Schedule::command('reports:generate')->dailyAt('01:00')->withoutOverlapping();

// Subscription state checks & renewal notices at 02:00
Schedule::command('subscriptions:check')->dailyAt('02:00')->withoutOverlapping();

// Data retention cleanup at 03:00
Schedule::command('data:cleanup')->dailyAt('03:00')->withoutOverlapping();

// Scheduled transactional email delivery at 08:00
Schedule::command('emails:send-scheduled')->dailyAt('08:00')->withoutOverlapping();

