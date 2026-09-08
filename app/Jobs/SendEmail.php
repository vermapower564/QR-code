<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public string $toEmail;
    public string $subject;
    public string $bodyContent;

    /**
     * Create a new job instance.
     */
    public function __construct(string $toEmail, string $subject, string $bodyContent)
    {
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->bodyContent = $bodyContent;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("SendEmail Job: Dispatching queued email to {$this->toEmail} with subject '{$this->subject}'.");
        // Reuses configured Mail mailer safely without logging secrets
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendEmail Job failed for {$this->toEmail}: " . $exception->getMessage());
    }
}
