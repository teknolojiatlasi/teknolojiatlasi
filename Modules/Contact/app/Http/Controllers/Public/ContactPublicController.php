<?php

namespace Modules\Contact\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Contact\Models\ContactMessage;
use Modules\Contact\Models\ContactSetting;

class ContactPublicController extends Controller
{
    public function index()
    {
        $settings = Cache::remember('public.contact.settings.v1', now()->addMinutes(30), fn () => ContactSetting::singleton());

        return view('contact::public.index', compact('settings'));
    }

    public function store(Request $request)
    {
        if ($request->filled('website')) {
            return response()->json([
                'ok' => true,
                'message' => 'Mesajiniz alindi.',
            ]);
        }

        $data = $request->validate([
            'contact_full_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'string', 'email', 'max:255'],
            'contact_subject' => ['required', 'string', 'max:255'],
            'contact_message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create([
            ...collect($data)->map(fn ($value) => is_string($value) ? trim($value) : $value)->all(),
            'contact_is_read' => false,
            'contact_is_replied' => false,
            'contact_meta' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Mesajınız alındı. En kısa sürede dönüş yapacağız.',
            ]);
        }

        return redirect()
            ->route('contact_public_index')
            ->with('status', 'Mesajınız alındı.');
    }
}
