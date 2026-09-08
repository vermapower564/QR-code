<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $userId;
    public string $documentType;
    public array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $documentType, array $data = [])
    {
        $this->userId = $userId;
        $this->documentType = $documentType;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("GeneratePDF Job: Building PDF document '{$this->documentType}' for user {$this->userId}.");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GeneratePDF Job failed for user {$this->userId}: " . $exception->getMessage());
    }
}
