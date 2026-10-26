<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;

class LandingPageController extends Controller
{
    /**
     * Display the public landing page.
     */
    public function __invoke()
    {
        return view('guest.landing-page');
    }
}
