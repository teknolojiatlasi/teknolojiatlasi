<?php

namespace App\Http\Middleware;

use App\Support\SecurityEventLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SensitiveRequestMonitor
{
    public function __construct(
        protected SecurityEventLogger $securityEventLogger,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->isSensitiveRequest($request)) {
            return $response;
        }

        if ($response->getStatusCode() >= 400) {
            $this->securityEventLogger->logSensitiveResponse($request, $response->getStatusCode(), [
                'admin_area' => $request->is('yonetim*', 'admin/*'),
                'auth_area' => $request->is('login', 'register', 'forgot-password', 'reset-password*', 'verify-email*', 'confirm-password', 'admin/mfa*'),
            ]);
        }

        return $response;
    }

    protected function isSensitiveRequest(Request $request): bool
    {
        return $request->is(
            'login',
            'logout',
            'register',
            'forgot-password',
            'reset-password',
            'reset-password/*',
            'verify-email',
            'verify-email/*',
            'confirm-password',
            'admin/mfa',
            'admin/mfa/*',
            'yonetim',
            'yonetim/*',
            'admin/*',
        );
    }
}
