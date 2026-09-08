<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAnalyticsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $userId;
    public string $startDate;
    public string $endDate;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $startDate, string $endDate)
    {
        $this->userId = $userId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->userId);

        if (!$user) {
            Log::warning("GenerateAnalyticsReport Job: User ID {$this->userId} not found.");
            return;
        }

        Log::info("GenerateAnalyticsReport Job: Compiling report for user {$user->id} between {$this->startDate} and {$this->endDate}.");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateAnalyticsReport Job failed for user ID {$this->userId}: " . $exception->getMessage());
    }
}
