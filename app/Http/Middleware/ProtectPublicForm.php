<?php

namespace App\Http\Middleware;

use App\Support\TurnstileVerifier;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectPublicForm
{
    public function __construct(
        protected TurnstileVerifier $turnstileVerifier,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->filled('_trap')) {
            abort(422, 'Form dogrulanamadi.');
        }

        $startedAt = (int) $request->input('_started_at', 0);
        $age = $startedAt > 0 ? now()->timestamp - $startedAt : null;

        if ($age === null || $age < 2 || $age > 10800) {
            abort(422, 'Form dogrulanamadi.');
        }

        $token = $request->input('cf-turnstile-response');

        if (! $this->turnstileVerifier->verify(is_string($token) ? $token : null, $request->ip())) {
            abort(422, 'Bot dogrulamasi basarisiz oldu.');
        }

        return $next($request);
    }
}
