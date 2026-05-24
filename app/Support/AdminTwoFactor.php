<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AdminTwoFactor
{
    public function sessionKey(): string
    {
        return 'admin_mfa_verified_at';
    }

    public function challengeWindowMinutes(): int
    {
        return (int) config('auth.admin_mfa.window', 720);
    }

    public function challengeExpiresMinutes(): int
    {
        return (int) config('auth.admin_mfa.challenge_expire', 10);
    }

    public function isRequiredFor(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return (! $user->hasRole('superadmin') && $user->hasRole('admin')) || (bool) $user->two_factor_required;
    }

    public function isVerified(User $user): bool
    {
        if (! $this->isRequiredFor($user)) {
            return true;
        }

        $verifiedAt = session($this->sessionKey());

        if (! is_numeric($verifiedAt)) {
            return false;
        }

        return now()->diffInMinutes(now()->setTimestamp((int) $verifiedAt)) <= $this->challengeWindowMinutes();
    }

    public function markVerified(): void
    {
        session([$this->sessionKey() => now()->timestamp]);
    }

    public function clearVerification(): void
    {
        session()->forget($this->sessionKey());
    }

    public function sendChallenge(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put($this->cacheKey($user), password_hash($code, PASSWORD_DEFAULT), now()->addMinutes($this->challengeExpiresMinutes()));
        Cache::put($this->throttleKey($user), now()->timestamp, now()->addMinutes(1));

        Mail::raw(
            "Yonetim giris dogrulama kodunuz: {$code}\nKod {$this->challengeExpiresMinutes()} dakika boyunca gecerlidir.",
            function ($message) use ($user): void {
                $message
                    ->to($user->email, $user->name)
                    ->subject(config('app.name') . ' yonetim giris dogrulama kodu');
            }
        );
    }

    public function canResend(User $user): bool
    {
        return ! Cache::has($this->throttleKey($user));
    }

    public function verifyChallenge(User $user, string $code): bool
    {
        $hash = Cache::get($this->cacheKey($user));

        if (! is_string($hash) || $hash === '') {
            return false;
        }

        $valid = password_verify(trim($code), $hash);

        if ($valid) {
            Cache::forget($this->cacheKey($user));
            Cache::forget($this->throttleKey($user));
        }

        return $valid;
    }

    protected function cacheKey(User $user): string
    {
        return 'admin-mfa:code:' . $user->getKey();
    }

    protected function throttleKey(User $user): string
    {
        return 'admin-mfa:resend:' . $user->getKey();
    }
}
