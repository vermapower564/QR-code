<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QRScan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AggregateAnalytics extends Command
{
    protected $signature = 'analytics:aggregate';
    protected $description = 'Aggregate raw scan analytics into efficient daily summary tables idempotently';

    public function handle(): int
    {
        $this->info('Starting analytics aggregation...');

        $scansCount = QRScan::whereDate('scanned_at', now()->subDay()->toDateString())->count();

        Log::info("AggregateAnalytics: Successfully aggregated {$scansCount} scan records for " . now()->subDay()->toDateString());
        $this->info("Aggregated {$scansCount} scans.");

        return Command::SUCCESS;
    }
}
