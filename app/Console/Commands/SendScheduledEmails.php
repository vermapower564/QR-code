<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\SendEmail;
use Illuminate\Support\Facades\Log;

class SendScheduledEmails extends Command
{
    protected $signature = 'emails:send-scheduled';
    protected $description = 'Queue daily digest notifications and scheduled user communication emails';

    public function handle(): int
    {
        $this->info('Queueing scheduled emails...');

        $users = User::where('onboarding_completed', true)->get();
        $queuedCount = 0;

        foreach ($users as $user) {
            // Queue weekly digest or update notification
            SendEmail::dispatch(
                $user->email,
                'Your Weekly QR Identity Analytics',
                "Hello {$user->name}, your dynamic QR profile performance digest is ready."
            );
            $queuedCount++;
        }

        Log::info("SendScheduledEmails: Queued {$queuedCount} scheduled emails.");
        $this->info("Queued {$queuedCount} scheduled emails.");

        return Command::SUCCESS;
    }
}
