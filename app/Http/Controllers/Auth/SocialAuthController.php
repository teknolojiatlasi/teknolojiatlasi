<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Spatie\Permission\Models\Role;

class SocialAuthController extends Controller
{
    private array $allowedProviders = ['google', 'github', 'facebook'];

    public function redirect(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        if (request()->filled('redirect')) {
            session(['social_auth_redirect' => request('redirect')]);
        } else {
            session()->forget('social_auth_redirect');
        }

        $driver = Socialite::driver($provider);

        if ($provider === 'github') {
            $driver->scopes(['user:email']);
        }

        if ($provider === 'facebook') {
            $driver->scopes(['email'])->with([
                'fields' => 'id,name,email,picture.type(large)',
            ]);
        }

        return $driver->redirect();
    }

    public function callback(string $provider)
    {
        if (!in_array($provider, $this->allowedProviders)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (InvalidStateException) {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        }

        if (!$socialUser->getEmail()) {
            abort(403, 'Email bilgisi alinamadi.');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
            ]);

            if (is_null($user->email_verified_at)) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            if (!$user->roles()->exists()) {
                Role::findOrCreate('member', 'web');
                $user->assignRole('member');
            }
        } else {
            $user = User::create([
                'name' => $socialUser->getName()
                    ?? $socialUser->getNickname()
                    ?? 'User',
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => Str::random(32),
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();
            Role::findOrCreate('member', 'web');
            $user->assignRole('member');
        }

        Auth::login($user);

        $redirectTo = session()->pull('social_auth_redirect', '/dashboard');

        return redirect()->to($redirectTo);
    }
}
