<?php

namespace App\Jobs;

use App\Models\QRProfile;
use App\Services\QRCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateBulkQR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $profileIds;

    /**
     * Create a new job instance.
     */
    public function __construct(array $profileIds)
    {
        $this->profileIds = $profileIds;
    }

    /**
     * Execute the job.
     */
    public function handle(QRCodeService $qrCodeService): void
    {
        Log::info("GenerateBulkQR Job: Processing batch of " . count($this->profileIds) . " profiles.");

        QRProfile::whereIn('id', $this->profileIds)->chunk(50, function ($profiles) use ($qrCodeService) {
            foreach ($profiles as $profile) {
                try {
                    $qrCodeService->generateForProfile($profile);
                } catch (\Throwable $e) {
                    Log::error("GenerateBulkQR Job: Failed for profile {$profile->id}: " . $e->getMessage());
                }
            }
        });
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateBulkQR Job failed: " . $exception->getMessage());
    }
}
