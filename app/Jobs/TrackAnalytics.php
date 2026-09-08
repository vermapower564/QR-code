<?php

namespace App\Jobs;

use App\Models\QRProfile;
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
    public string $eventType;
    public int $linkId;
    public array $requestData;

    /**
     * Create a new job instance.
     */
    public function __construct(int $profileId, string $eventType = 'qr_scan', int $linkId = 0, array $requestData = [])
    {
        $this->profileId = $profileId;
        $this->eventType = $eventType;
        $this->linkId = $linkId;
        $this->requestData = $requestData;
    }

    /**
     * Execute the job.
     */
    public function handle(AnalyticsService $analyticsService): void
    {
        try {
            $profile = QRProfile::find($this->profileId);
            if (!$profile) {
                return;
            }

            // Create synthetic request wrapper
            $request = new \Illuminate\Http\Request();
            if (isset($this->requestData['ip'])) {
                $request->server->set('REMOTE_ADDR', $this->requestData['ip']);
            }
            if (isset($this->requestData['user_agent'])) {
                $request->headers->set('User-Agent', $this->requestData['user_agent']);
            }
            if (isset($this->requestData['referer'])) {
                $request->headers->set('Referer', $this->requestData['referer']);
            }
            if (isset($this->requestData['country'])) {
                $request->headers->set('cf-ipcountry', $this->requestData['country']);
            }

            if ($this->eventType === 'qr_scan') {
                $analyticsService->recordScan($profile, $request);
            } else {
                $analyticsService->recordClick($profile, $this->linkId, $request, $this->eventType);
            }
        } catch (\Throwable $e) {
            Log::error("TrackAnalytics Job failed for profile ID {$this->profileId}: " . $e->getMessage());
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("TrackAnalytics Job failed permanently for profile ID {$this->profileId}: " . $exception->getMessage());
    }
}
