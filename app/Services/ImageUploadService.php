<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    protected array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    protected int $maxSizeBytes = 5242880; // 5 MB default limit

    /**
     * Upload an image with multi-layer MIME, extension, size, and UUID filename verification.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): string
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Uploaded file is corrupted or invalid.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \InvalidArgumentException('Invalid file extension. Only JPG, PNG, and WEBP allowed.');
        }

        $mime = strtolower($file->getMimeType());
        if (!in_array($mime, $this->allowedMimes)) {
            throw new \InvalidArgumentException('Invalid MIME type. Uploaded file is not a valid image.');
        }

        if ($file->getSize() > $this->maxSizeBytes) {
            throw new \InvalidArgumentException('File size exceeds maximum allowed limit of 5 MB.');
        }

        // Generate cryptographically random UUID filename
        $uuidName = Str::uuid()->toString() . '.' . $extension;

        return $file->storeAs($folder, $uuidName, $disk);
    }
}
