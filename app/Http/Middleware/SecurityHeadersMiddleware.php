<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'SAMEORIGIN');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-XSS-Protection', '1; mode=block');
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->header('Content-Security-Policy', "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';");
        }

        return $response;
    }
}
