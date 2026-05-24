<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileVerifier
{
    public function isEnabled(): bool
    {
        return (bool) config('services.turnstile.enabled')
            && filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    public function verify(?string $token, ?string $ipAddress = null): bool
    {
        if (! $this->isEnabled()) {
            return true;
        }

        if (! is_string($token) || trim($token) === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array_filter([
                    'secret' => (string) config('services.turnstile.secret_key'),
                    'response' => trim($token),
                    'remoteip' => $ipAddress,
                ]));

            if (! $response->ok()) {
                Log::warning('Turnstile verification request failed', [
                    'status' => $response->status(),
                ]);

                return false;
            }

            return (bool) $response->json('success', false);
        } catch (\Throwable $exception) {
            Log::warning('Turnstile verification exception', [
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
