<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    protected function contentSecurityPolicy(): string
    {
        $policy = config('security.csp', []);
        $directives = [];

        foreach ($policy as $directive => $value) {
            if ($directive === 'upgrade_insecure_requests') {
                if ($value) {
                    $directives[] = 'upgrade-insecure-requests';
                }

                continue;
            }

            $name = str_replace('_', '-', $directive);
            $sources = array_values(array_filter((array) $value));

            if ($sources === []) {
                continue;
            }

            $directives[] = $name . ' ' . implode(' ', $sources);
        }

        return implode('; ', $directives);
    }
}
