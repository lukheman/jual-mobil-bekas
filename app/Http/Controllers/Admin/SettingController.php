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
            'settings' => Setting::query()->pluck('value', 'key'),
            'fields' => config('showroom.settings'),
        ]);
    }

    /**
     * Update the site settings.
     */
    public function update(SettingRequest $request): RedirectResponse
    {
        $settings = $request->validated('settings', []);

        foreach (array_keys(config('showroom.settings')) as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $settings[$key] ?? null]
            );
        }

        return back()->with('success', 'Pengaturan situs berhasil disimpan.');
    }
}
