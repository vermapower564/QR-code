<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\GenerateAnalyticsReport;
use Illuminate\Support\Facades\Log;

class GenerateScheduledReports extends Command
{
    protected $signature = 'reports:generate';
    protected $description = 'Trigger queued generation of scheduled analytics reports';

    public function handle(): int
    {
        $this->info('Triggering scheduled reports generation...');

        $users = User::whereHas('qrProfiles')->get();
        $dispatchedCount = 0;

        foreach ($users as $user) {
            GenerateAnalyticsReport::dispatch(
                $user->id,
                now()->subDays(7)->toDateString(),
                now()->toDateString()
            );
            $dispatchedCount++;
        }

        Log::info("GenerateScheduledReports: Queued {$dispatchedCount} analytics report jobs.");
        $this->info("Queued {$dispatchedCount} reports.");

        return Command::SUCCESS;
    }
}
