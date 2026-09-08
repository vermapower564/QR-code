<?php

namespace App\Jobs;

use App\Models\QRCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessQRCode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $qrCodeId;
    public string $format;

    /**
     * Create a new job instance.
     */
    public function __construct(int $qrCodeId, string $format = 'png')
    {
        $this->qrCodeId = $qrCodeId;
        $this->format = $format;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $qrCode = QRCode::find($this->qrCodeId);

        if (!$qrCode) {
            Log::warning("ProcessQRCode Job: QR Code ID {$this->qrCodeId} not found.");
            return;
        }

        Log::info("ProcessQRCode Job: Processing QR {$qrCode->id} for format {$this->format}.");
        $qrCode->update(['status' => 'processed']);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessQRCode Job failed for QR ID {$this->qrCodeId}: " . $exception->getMessage());
    }
}
