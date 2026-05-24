<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.force_https') || app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        if (! $this->isSecureRequest($request)) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }

    protected function isSecureRequest(Request $request): bool
    {
        if ($request->isSecure()) {
            return true;
        }

        if (strtolower((string) $request->header('X-Forwarded-Proto')) === 'https') {
            return true;
        }

        $cfVisitor = (string) $request->header('CF-Visitor');

        return str_contains(strtolower($cfVisitor), '"scheme":"https"');
    }
}
