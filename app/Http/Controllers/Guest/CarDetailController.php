<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Setting;
use Illuminate\View\View;

class CarDetailController extends Controller
{
    public function show(Car $car): View
    {
        abort_unless($car->status->value === 'available', 404);
        $car->load(['carModel.brand', 'photos', 'features']);
        $car->increment('views_count');

        return view('guest.car-detail', [
            'car' => $car,
            'settings' => Setting::query()->pluck('value', 'key'),
        ]);
    }
}
