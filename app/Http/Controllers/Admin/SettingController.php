<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the site settings form.
     */
    public function edit(): View
    {
        return view('admin.settings.edit', [
            // Kirim nilai database sebagai array agar setiap field form terisi sesuai key-nya.
            'settings' => Setting::query()->pluck('value', 'key')->toArray(),
            'fields' => config('showroom.settings'),
        ]);
    }

    /**
     * Update the site settings.
     */
    public function update(SettingRequest $request): RedirectResponse
    {
        // Ambil nested payload langsung. Form menyimpan nilai dengan nama settings[key].
        $settings = $request->input('settings', []);

        foreach (array_keys(config('showroom.settings')) as $key) {
            if (! array_key_exists($key, $settings)) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => trim((string) $settings[$key]) ?: null]
            );
        }

        return back()->with('success', 'Pengaturan situs berhasil disimpan.');
    }
}
