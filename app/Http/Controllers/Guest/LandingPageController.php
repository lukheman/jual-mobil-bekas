<?php

namespace App\Http\Controllers\Guest;

use App\Enums\BodyType;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Faq;
use App\Models\Setting;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Display the public landing page.
     */
    public function __invoke(): View
    {
        $filters = request()->only(['brand', 'body_type', 'max_price']);
        $carQuery = Car::query()
            ->available()
            ->with(['carModel.brand', 'primaryPhoto']);

        $carQuery->when($filters['brand'] ?? null, function ($query, string $brand): void {
            $query->whereHas('carModel.brand', fn ($brandQuery) => $brandQuery->where('slug', $brand));
        });
        $carQuery->when($filters['body_type'] ?? null, fn ($query, string $bodyType) => $query->where('body_type', $bodyType));
        $carQuery->when($filters['max_price'] ?? null, fn ($query, string $maxPrice) => $query->where('price', '<=', (int) $maxPrice));

        $featuredCars = (clone $carQuery)
            ->featured()
            ->latest()
            ->limit(4)
            ->get();

        $latestCars = (clone $carQuery)
            ->latest()
            ->limit(8)
            ->get();

        return view('guest.landing-page', [
            'featuredCars' => $featuredCars,
            'latestCars' => $latestCars,
            'brands' => Brand::query()->withCount(['cars' => fn ($query) => $query->available()])->orderBy('name')->get(),
            'faqs' => Faq::published()->limit(5)->get(),
            'settings' => Setting::query()->pluck('value', 'key'),
            'bodyTypes' => BodyType::cases(),
            'transmissions' => Transmission::cases(),
            'fuelTypes' => FuelType::cases(),
        ]);
    }
}
