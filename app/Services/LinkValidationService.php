<?php

namespace App\Services;

class LinkValidationService
{
    /**
     * Allowed URL protocols.
     */
    protected array $allowedSchemes = ['http', 'https'];

    /**
     * Rejected dangerous URL schemes.
     */
    protected array $rejectedSchemes = ['javascript', 'data', 'file', 'vbscript', 'ftp'];

    /**
     * Map of social platforms to allowed root domains.
     */
    protected array $platformDomains = [
        'instagram' => ['instagram.com', 'www.instagram.com', 'instagr.am'],
        'facebook'  => ['facebook.com', 'www.facebook.com', 'fb.com', 'm.facebook.com'],
        'linkedin'  => ['linkedin.com', 'www.linkedin.com'],
        'youtube'   => ['youtube.com', 'www.youtube.com', 'youtu.be'],
        'twitter'   => ['twitter.com', 'www.twitter.com', 'x.com', 'www.x.com'],
        'x'         => ['twitter.com', 'www.twitter.com', 'x.com', 'www.x.com'],
        'whatsapp'  => ['whatsapp.com', 'www.whatsapp.com', 'wa.me', 'api.whatsapp.com'],
        'github'    => ['github.com', 'www.github.com'],
        'tiktok'    => ['tiktok.com', 'www.tiktok.com', 'vm.tiktok.com'],
    ];

    /**
     * Normalize a user-submitted URL by trimming whitespace, lowercasing scheme,
     * and automatically prepending 'https://' if no scheme is provided.
     */
    public function normalizeUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $url = trim($url);
        if ($url === '') {
            return '';
        }

        // Auto-prepend https:// if no scheme is present (e.g. example.com or www.example.com)
        if (!preg_match('~^[a-z0-9+\-.]+://~i', $url)) {
            $url = 'https://' . $url;
        }

        // Normalize scheme casing (e.g. HTTPS://example.com -> https://example.com)
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme && strtolower($scheme) !== $scheme) {
            $url = strtolower($scheme) . substr($url, strlen($scheme));
        }

        return $url;
    }

    /**
     * Validate a user-submitted URL for safe scheme and format.
     */
    public function isValidUrl(string $url): bool
    {
        $normalized = $this->normalizeUrl($url);

        if (empty($normalized) || strlen($normalized) > 2048) {
            return false;
        }

        // Filter control characters
        if (preg_match('/[\x00-\x1F\x7F]/', $normalized)) {
            return false;
        }

        $scheme = parse_url($normalized, PHP_URL_SCHEME);
        if (!$scheme || !in_array(strtolower($scheme), $this->allowedSchemes, true)) {
            return false;
        }

        $host = parse_url($normalized, PHP_URL_HOST);
        if (!$host || filter_var($normalized, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return true;
    }

    /**
     * Validate platform-specific social links.
     * Host matching verifies host against allowed list to reject fake/spoofed domains.
     */
    public function validatePlatformUrl(string $platform, string $url): bool
    {
        if (!$this->isValidUrl($url)) {
            return false;
        }

        $platformKey = strtolower(trim($platform));

        if (!isset($this->platformDomains[$platformKey])) {
            return true; // Generic platform or custom link
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowed = $this->platformDomains[$platformKey];

        return in_array($host, $allowed, true);
    }

    /**
     * Sanitize user input for formula injection prevention in CSV exports.
     */
    public function sanitizeCsvCell(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $firstChar = substr($value, 0, 1);
        if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $value;
        }

        return $value;
    }
}
