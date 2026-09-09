<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $fail("The {$attribute} must be a valid URL.");
            return;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);
        if (!in_array(strtolower($scheme), ['http', 'https'])) {
            $fail("The {$attribute} must be a secure HTTP or HTTPS URL.");
            return;
        }

        $host = parse_url($value, PHP_URL_HOST);
        $ips = gethostbynamel($host);

        if (!$ips) {
            $fail("The {$attribute} host could not be resolved.");
            return;
        }

        foreach ($ips as $ip) {
            // Check for private, loopback, or reserved IPs
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $fail("The {$attribute} resolves to a restricted internal network address (SSRF Protection).");
                return;
            }
        }
    }
}
