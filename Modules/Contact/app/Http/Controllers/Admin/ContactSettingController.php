<?php

namespace Modules\Contact\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Contact\Models\ContactSetting;

class ContactSettingController extends Controller
{
    public function edit()
    {
        $settings = ContactSetting::singleton();

        return view('contact::admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'contact_company_name' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'contact_city' => ['nullable', 'string', 'max:255'],
            'contact_district' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'string', 'email', 'max:255'],
            'contact_lat' => ['nullable', 'numeric'],
            'contact_lng' => ['nullable', 'numeric'],
        ]);

        $settings = ContactSetting::singleton();
        $settings->update([
            ...$data,
            'contact_updated_by_id' => (int) optional($request->user())->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Ayarlar kaydedildi.']);
        }

        return redirect()->route('contact_admin_settings_edit')->with('status', 'Ayarlar kaydedildi.');
    }
}

