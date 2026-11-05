<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('guest.about', [
            'settings' => Setting::query()->pluck('value', 'key'),
        ]);
    }
}
