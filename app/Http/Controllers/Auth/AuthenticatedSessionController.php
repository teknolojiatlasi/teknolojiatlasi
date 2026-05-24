<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\AdminTwoFactor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        protected AdminTwoFactor $adminTwoFactor,
    ) {
    }

    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $this->storeIntendedUrl($request);

        return view('auth.login', [
            'redirect' => $request->query('redirect'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $this->storeIntendedUrl($request);

        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        if ($user && !$user->roles()->exists()) {
            Role::findOrCreate('member', 'web');
            $user->assignRole('member');
        }

        if ($user && $this->adminTwoFactor->isRequiredFor($user)) {
            $this->adminTwoFactor->clearVerification();

            try {
                $this->adminTwoFactor->sendChallenge($user);
            } catch (\Throwable $exception) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Yonetim giris kodu gonderilemedi. E-posta ayarlarinizi kontrol edin.',
                ]);
            }

            return redirect()->route('admin.mfa.challenge');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $this->adminTwoFactor->clearVerification();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function storeIntendedUrl(Request $request): void
    {
        $redirect = (string) $request->input('redirect', $request->query('redirect', ''));

        if ($redirect === '') {
            return;
        }

        if (str_starts_with($redirect, '/')) {
            $request->session()->put('url.intended', $redirect);
            return;
        }

        $appUrl = rtrim(url('/'), '/');
        if (str_starts_with($redirect, $appUrl)) {
            $request->session()->put('url.intended', substr($redirect, strlen($appUrl)) ?: '/');
        }
    }
}
