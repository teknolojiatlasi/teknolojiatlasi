<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SecurityEventLogger
{
    public function logAuthFailure(Request $request, string $event, array $context = []): void
    {
        $this->write('warning', 'auth_failure', $request, array_merge([
            'event' => $event,
        ], $context));

        $this->trackBurst($request, 'auth_failure', array_merge([
            'event' => $event,
        ], $context));
    }

    public function logAuthSuccess(Request $request, string $event, array $context = []): void
    {
        $this->write('info', 'auth_success', $request, array_merge([
            'event' => $event,
        ], $context));
    }

    public function logSensitiveResponse(Request $request, int $statusCode, array $context = []): void
    {
        $level = $statusCode >= 500 ? 'error' : 'warning';

        $this->write($level, 'sensitive_response', $request, array_merge([
            'status' => $statusCode,
        ], $context));

        if (in_array($statusCode, [401, 403, 404, 422, 429], true)) {
            $this->trackBurst($request, 'sensitive_response', array_merge([
                'status' => $statusCode,
            ], $context));
        }
    }

    protected function trackBurst(Request $request, string $reason, array $context = []): void
    {
        $window = max(1, (int) config('security.alerts.burst_window_minutes', 10));
        $threshold = max(1, (int) config('security.alerts.burst_threshold', 10));
        $ip = (string) $request->ip();
        $key = 'security-burst:' . sha1($reason . '|' . $ip);
        $count = Cache::increment($key);

        if ($count === 1) {
            Cache::put($key, 1, now()->addMinutes($window));
        }

        if ($count >= $threshold) {
            $this->write('critical', 'security_burst_detected', $request, array_merge([
                'reason' => $reason,
                'count' => $count,
                'window_minutes' => $window,
            ], $context));
        }
    }

    protected function write(string $level, string $message, Request $request, array $context = []): void
    {
        Log::channel('security')->log($level, $message, array_merge([
            'ip' => $request->ip(),
            'forwarded_for' => $request->header('X-Forwarded-For'),
            'method' => $request->method(),
            'path' => '/' . ltrim($request->path(), '/'),
            'route' => optional($request->route())->getName(),
            'user_id' => $request->user()?->getAuthIdentifier(),
            'user_agent' => (string) $request->userAgent(),
        ], $context));
    }
}
