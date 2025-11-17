<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $csp = "default-src 'self' data: blob: https:; "
             . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; "
             . "style-src 'self' 'unsafe-inline' https:; "
             . "img-src 'self' data: blob: https:; "
             . "font-src 'self' data: https:; "
             . "connect-src 'self' https: wss:; "
             . "media-src 'self' data: blob: https:; "
             . "worker-src 'self'; "
             . "manifest-src 'self'; "
             . "frame-ancestors 'self';";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
