<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes & Scheduler Definitions
|--------------------------------------------------------------------------
|
| Section 62 — Daily Cron Tasks:
| 1. Delete expired temporary files
| 2. Aggregate analytics
| 3. Generate reports
| 4. Subscription checks
| 5. Cleanup old data
| 6. Send scheduled emails
|
*/

Schedule::command('clean:temp-files')->daily()->withoutOverlapping();
Schedule::command('analytics:aggregate')->dailyAt('00:05')->withoutOverlapping();
Schedule::command('reports:generate')->dailyAt('01:00')->withoutOverlapping();
Schedule::command('subscriptions:check')->dailyAt('02:00')->withoutOverlapping();
Schedule::command('data:cleanup')->dailyAt('03:00')->withoutOverlapping();
Schedule::command('emails:send-scheduled')->dailyAt('08:00')->withoutOverlapping();
