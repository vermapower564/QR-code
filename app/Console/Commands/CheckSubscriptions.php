<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class CheckSubscriptions extends Command
{
    protected $signature = 'subscriptions:check';
    protected $description = 'Inspect active subscriptions for renewal, expiration, trial period, or past-due payment';

    public function handle(): int
    {
        $this->info('Checking subscription statuses...');

        $expiredSubscriptions = Subscription::where('status', 'active')
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            Log::info("CheckSubscriptions: Marked subscription {$subscription->id} as expired.");
        }

        $this->info("Processed " . $expiredSubscriptions->count() . " expired subscriptions.");

        return Command::SUCCESS;
    }
}
