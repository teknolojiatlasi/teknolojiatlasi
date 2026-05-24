<?php

namespace App\Http\Middleware;

use App\Support\AdminTwoFactor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminTwoFactor
{
    public function __construct(
        protected AdminTwoFactor $adminTwoFactor,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $this->adminTwoFactor->isRequiredFor($user)) {
            return $next($request);
        }

        if ($this->adminTwoFactor->isVerified($user)) {
            return $next($request);
        }

        return redirect()->route('admin.mfa.challenge');
    }
}
