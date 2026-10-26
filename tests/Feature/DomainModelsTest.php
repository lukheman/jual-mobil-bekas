<?php

use App\Enums\BodyType;
use App\Enums\CarStatus;
use App\Enums\ClickType;
use App\Enums\FuelType;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\Transmission;
use App\Enums\UserRole;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarPhoto;
use App\Models\ClickLog;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;

it('creates cars with generated unique slugs and enum casts', function () {
    $brand = Brand::factory()->create(['name' => 'Toyota']);
    $carModel = CarModel::factory()->for($brand, 'brand')->create(['name' => 'Avanza']);

    $first = Car::factory()->for($carModel, 'carModel')->create([
        'variant' => null,
        'year' => 2018,
        'status' => CarStatus::Available,
        'transmission' => Transmission::Automatic,
        'fuel_type' => FuelType::Petrol,
        'body_type' => BodyType::Mpv,
    ]);

    $second = Car::factory()->for($carModel, 'carModel')->create([
        'variant' => null,
        'year' => 2018,
    ]);

    expect($first->slug)->toBe('toyota-avanza-2018')
        ->and($second->slug)->toBe('toyota-avanza-2018-2')
        ->and($first->status)->toBe(CarStatus::Available)
        ->and($first->transmission)->toBe(Transmission::Automatic)
        ->and($first->fuel_type)->toBe(FuelType::Petrol)
        ->and($first->body_type)->toBe(BodyType::Mpv)
        ->and($first->is_featured)->toBeBool()
        ->and($first->carModel->is($carModel))->toBeTrue()
        ->and($first->carModel->brand->is($brand))->toBeTrue();
});

it('attaches photos and features to a car', function () {
    $car = Car::factory()->create();
    $primary = CarPhoto::factory()->for($car, 'car')->primary()->create(['sort_order' => 1]);
    CarPhoto::factory()->for($car, 'car')->create(['sort_order' => 0]);

    $features = Feature::factory()->count(2)->create();

    $car->features()->attach($features);

    expect($car->photos()->count())->toBe(2)
        ->and($car->primaryPhoto->is($primary))->toBeTrue()
        ->and($car->features)->toHaveCount(2);
});

it('stores leads and click logs with enum casts', function () {
    $car = Car::factory()->create();

    $lead = Lead::factory()->for($car, 'car')->testDrive()->create([
        'status' => LeadStatus::Contacted,
    ]);

    $click = ClickLog::factory()->for($car, 'car')->phone()->create();

    expect($lead->type)->toBe(LeadType::TestDrive)
        ->and($lead->status)->toBe(LeadStatus::Contacted)
        ->and($lead->preferred_schedule)->not->toBeNull()
        ->and($lead->consent_at)->not->toBeNull()
        ->and($lead->car->is($car))->toBeTrue()
        ->and($click->type)->toBe(ClickType::Phone)
        ->and($click->created_at)->not->toBeNull()
        ->and($click->updated_at)->toBeNull()
        ->and($click->car->is($car))->toBeTrue();
});

it('stores pages, faqs, settings, and casts user roles', function () {
    $page = Page::factory()->create(['title' => 'Tentang Kami', 'slug' => null]);
    $faq = Faq::factory()->create(['sort_order' => 2]);
    $setting = Setting::factory()->create(['key' => 'whatsapp_number', 'value' => '6281234567890']);
    $admin = User::factory()->create();

    expect($page->slug)->toBe('tentang-kami')
        ->and($page->is_published)->toBeTrue()
        ->and($setting->value)->toBe('6281234567890')
        ->and(Faq::published()->pluck('id'))->toContain($faq->id)
        ->and($admin->role)->toBe(UserRole::Admin)
        ->and($admin->isAdmin())->toBeTrue();
});
