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

class GenerateQRCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;
    public int $profileId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $profileId)
    {
        $this->profileId = $profileId;
    }

    /**
     * Execute the job.
     */
    public function handle(QRCodeService $qrCodeService): void
    {
        $profile = QRProfile::find($this->profileId);

        if (!$profile) {
            Log::warning("GenerateQRCode Job: Profile ID {$this->profileId} not found.");
            return;
        }

        $targetUrl = url("/p/{$profile->slug}");

        Log::info("GenerateQRCode Job: Generating QR for profile {$profile->id} ({$targetUrl}).");

        $qrCodeService->generateForProfile($profile);

        if ($profile->qrCode) {
            $profile->qrCode->update(['status' => 'completed']);
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateQRCode Job failed for profile ID {$this->profileId}: " . $exception->getMessage());
    }
}
