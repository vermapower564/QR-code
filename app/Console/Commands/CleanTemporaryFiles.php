<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CleanTemporaryFiles extends Command
{
    protected $signature = 'clean:temp-files';
    protected $description = 'Delete expired temporary files safely without deleting active user profile uploads or QR assets';

    public function handle(): int
    {
        $this->info('Starting temporary file cleanup...');

        $tempFiles = Storage::files('temp');
        $deletedCount = 0;

        foreach ($tempFiles as $file) {
            if (Storage::lastModified($file) < (time() - 86400)) { // 24 hours
                Storage::delete($file);
                $deletedCount++;
            }
        }

        Log::info("CleanTemporaryFiles: Deleted {$deletedCount} expired temporary files.");
        $this->info("Deleted {$deletedCount} temporary files.");

        return Command::SUCCESS;
    }
}
