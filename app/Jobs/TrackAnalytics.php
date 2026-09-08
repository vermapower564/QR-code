<?php

namespace App\Jobs;

use App\Services\AnalyticsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TrackAnalytics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $profileId;
    public string $ipAddress;
    public ?string $userAgent;
    public ?string $referrer;

    /**
     * Create a new job instance.
     */
    public function __construct(int $profileId, string $ipAddress, ?string $userAgent = null, ?string $referrer = null)
    {
        $this->profileId = $profileId;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->referrer = $referrer;
    }

    /**
     * Execute the job.
     */
    public function handle(AnalyticsService $analyticsService): void
    {
        Log::info("TrackAnalytics Job: Logging scan for profile ID {$this->profileId}.");
        $analyticsService->recordScan($this->profileId, $this->ipAddress, $this->userAgent, $this->referrer);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("TrackAnalytics Job failed for profile ID {$this->profileId}: " . $exception->getMessage());
    }
}
