<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\AdminTwoFactor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminTwoFactorChallengeController extends Controller
{
    public function __construct(
        protected AdminTwoFactor $adminTwoFactor,
    ) {
    }

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $this->adminTwoFactor->isRequiredFor($user)) {
            return redirect()->route('dashboard');
        }

        if ($this->adminTwoFactor->isVerified($user)) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($this->adminTwoFactor->canResend($user)) {
            $this->adminTwoFactor->sendChallenge($user);
            session()->flash('status', 'Dogrulama kodu e-posta adresinize gonderildi.');
        }

        return view('auth.admin-two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $this->adminTwoFactor->isRequiredFor($user)) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (! $this->adminTwoFactor->verifyChallenge($user, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'Kod hatali veya suresi dolmus.',
            ]);
        }

        $this->adminTwoFactor->markVerified();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || ! $this->adminTwoFactor->isRequiredFor($user)) {
            return redirect()->route('dashboard');
        }

        if (! $this->adminTwoFactor->canResend($user)) {
            throw ValidationException::withMessages([
                'code' => 'Yeni kod istemeden once kisa bir sure bekleyin.',
            ]);
        }

        $this->adminTwoFactor->sendChallenge($user);

        return back()->with('status', 'Yeni dogrulama kodu gonderildi.');
    }
}
