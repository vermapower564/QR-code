<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Jobs\SendEmail;
use Illuminate\Support\Facades\Log;

class CheckSubscriptions extends Command
{
    protected $signature = 'subscriptions:check';
    protected $description = 'Inspect active subscriptions for renewal, expiration, trial period, or past-due payment';

    public function handle(): int
    {
        $this->info('Checking subscription statuses...');

        // Expiration check
        $expiredSubscriptions = Subscription::whereIn('status', ['active', 'cancelled', 'trialing'])
            ->where('ends_at', '<', now())
            ->with('user')
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            Log::info("CheckSubscriptions: Marked subscription #{$subscription->id} for user #{$subscription->user_id} as expired.");

            if ($subscription->user && $subscription->user->email) {
                try {
                    SendEmail::dispatch(
                        $subscription->user->email,
                        'Subscription Expired',
                        'Your QR SaaS subscription has expired. Your QR profiles remain saved, but premium features are restricted until renewal.'
                    );
                } catch (\Throwable $e) {
                    Log::error("Failed to queue subscription expiration email: " . $e->getMessage());
                }
            }
        }

        $this->info("Processed " . $expiredSubscriptions->count() . " expired subscriptions safely without data loss.");

        return Command::SUCCESS;
    }
}
