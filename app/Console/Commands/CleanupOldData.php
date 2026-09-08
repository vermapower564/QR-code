<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupOldData extends Command
{
    protected $signature = 'data:cleanup';
    protected $description = 'Clean expired password tokens, temporary sessions, and stale queue logs based on retention rules';

    public function handle(): int
    {
        $this->info('Starting old data cleanup...');

        $deletedTokens = DB::table('password_resets')
            ->where('created_at', '<', now()->subHours(24))
            ->delete();

        Log::info("CleanupOldData: Cleared {$deletedTokens} expired password reset tokens.");
        $this->info("Cleared {$deletedTokens} expired password reset tokens.");

        return Command::SUCCESS;
    }
}
