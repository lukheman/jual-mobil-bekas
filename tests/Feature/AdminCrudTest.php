<?php

use App\Enums\CarStatus;
use App\Enums\LeadStatus;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarPhoto;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create();
});

test('guests cannot access the admin crud pages', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect('/login');
})->with([
    'cars' => 'admin.cars',
    'cars create' => 'admin.cars.create',
    'brands' => 'admin.brands',
    'car models' => 'admin.car-models',
    'features' => 'admin.features',
    'leads' => 'admin.leads',
    'settings' => 'admin.settings',
    'pages' => 'admin.pages',
    'faqs' => 'admin.faqs',
]);

test('an admin can view every crud index page', function () {
    $brand = Brand::factory()->create(['name' => 'Toyota']);
    $carModel = CarModel::factory()->for($brand, 'brand')->create(['name' => 'Avanza']);
    Car::factory()->for($carModel, 'carModel')->create(['city' => 'Samarinda']);
    Feature::factory()->create();
    Lead::factory()->create();
    Page::factory()->create();
    Faq::factory()->create();

    $this->actingAs($this->admin)->get(route('admin.cars'))
        ->assertOk()
        ->assertSee('Tambah Mobil')
        ->assertSee('Toyota Avanza');

    $this->actingAs($this->admin)->get(route('admin.brands'))
        ->assertOk()
        ->assertSee('Daftar Merek');

    $this->actingAs($this->admin)->get(route('admin.car-models'))
        ->assertOk()
        ->assertSee('Tambah Model');

    $this->actingAs($this->admin)->get(route('admin.features'))
        ->assertOk()
        ->assertSee('Daftar Fitur');

    $this->actingAs($this->admin)->get(route('admin.leads'))
        ->assertOk()
        ->assertSee('Ekspor CSV');

    $this->actingAs($this->admin)->get(route('admin.pages'))
        ->assertOk()
        ->assertSee('Daftar Halaman');

    $this->actingAs($this->admin)->get(route('admin.faqs'))
        ->assertOk()
        ->assertSee('Daftar FAQ');
});

test('crud index pages render their empty states', function () {
    foreach ([
        'admin.cars',
        'admin.brands',
        'admin.car-models',
        'admin.features',
        'admin.leads',
        'admin.pages',
        'admin.faqs',
    ] as $routeName) {
        $this->actingAs($this->admin)->get(route($routeName))->assertOk();
    }
});

test('an admin can create update archive and restore a car', function () {
    Storage::fake('public');

    $brand = Brand::factory()->create(['name' => 'Toyota']);
    $carModel = CarModel::factory()->for($brand, 'brand')->create(['name' => 'Avanza']);
    $feature = Feature::factory()->create(['name' => 'Bebas banjir']);

    $this->actingAs($this->admin)
        ->get(route('admin.cars.create'))
        ->assertOk()
        ->assertSee('Tambah Mobil')
        ->assertSee('Toyota — Avanza');

    $photos = [
        UploadedFile::fake()->image('depan.jpg', 1200, 800),
        UploadedFile::fake()->image('belakang.jpg', 1200, 800),
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.cars.store'), [
        'car_model_id' => $carModel->id,
        'variant' => 'G',
        'year' => 2020,
        'price' => 215000000,
        'mileage' => 45000,
        'transmission' => 'automatic',
        'fuel_type' => 'petrol',
        'engine_capacity' => 1500,
        'body_type' => 'mpv',
        'color' => 'Putih',
        'owner_count' => 1,
        'plate_area' => 'KT',
        'tax_expiry' => now()->addMonths(6)->format('Y-m-d'),
        'document_completeness' => 'Lengkap',
        'condition' => 'Terawat dan siap pakai.',
        'description' => 'Unit pilihan dengan riwayat perawatan lengkap.',
        'city' => 'Samarinda',
        'status' => 'available',
        'is_featured' => '1',
        'features' => [$feature->id],
        'photos' => $photos,
    ]);

    $car = Car::query()->where('year', 2020)->firstOrFail();

    $response->assertRedirect(route('admin.cars.edit', $car));

    expect($car->slug)->toBe('toyota-avanza-2020-g')
        ->and($car->status)->toBe(CarStatus::Available)
        ->and($car->is_featured)->toBeTrue()
        ->and($car->features->pluck('id'))->toContain($feature->id)
        ->and($car->photos)->toHaveCount(2)
        ->and($car->photos()->where('is_primary', true)->count())->toBe(1);

    foreach ($car->photos as $photo) {
        Storage::disk('public')->assertExists($photo->path);
        Storage::disk('public')->assertExists('cars/thumbs/'.basename($photo->path));
    }

    $this->actingAs($this->admin)
        ->get(route('admin.cars.edit', $car))
        ->assertOk()
        ->assertSee('Edit Mobil')
        ->assertSee('Kelola Foto');

    $this->actingAs($this->admin)
        ->put(route('admin.cars.update', $car), [
            'car_model_id' => $carModel->id,
            'variant' => 'G',
            'year' => 2020,
            'price' => 205000000,
            'mileage' => 46000,
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'engine_capacity' => 1500,
            'body_type' => 'mpv',
            'color' => 'Hitam',
            'owner_count' => 2,
            'plate_area' => 'KT',
            'tax_expiry' => now()->addMonths(5)->format('Y-m-d'),
            'document_completeness' => 'Lengkap',
            'condition' => 'Terawat.',
            'description' => 'Deskripsi terbaru.',
            'city' => 'Balikpapan',
            'status' => 'reserved',
            'features' => [],
            'photos' => [UploadedFile::fake()->image('interior.jpg', 1000, 700)],
        ])
        ->assertRedirect(route('admin.cars.edit', $car));

    $car->refresh();

    expect($car->price)->toBe(205000000)
        ->and($car->status)->toBe(CarStatus::Reserved)
        ->and($car->city)->toBe('Balikpapan')
        ->and($car->features)->toHaveCount(0)
        ->and($car->photos)->toHaveCount(3)
        ->and($car->is_featured)->toBeFalse();

    $this->actingAs($this->admin)
        ->post(route('admin.cars.status', $car), ['status' => 'sold'])
        ->assertRedirect();

    expect($car->fresh()->status)->toBe(CarStatus::Sold);

    $this->actingAs($this->admin)
        ->post(route('admin.cars.featured', $car))
        ->assertRedirect();

    expect($car->fresh()->is_featured)->toBeTrue();

    $this->actingAs($this->admin)
        ->delete(route('admin.cars.destroy', $car))
        ->assertRedirect(route('admin.cars'));

    expect($car->fresh()->trashed())->toBeTrue();

    $this->actingAs($this->admin)
        ->get(route('admin.cars', ['arsip' => 1]))
        ->assertOk()
        ->assertSee('Diarsipkan');

    $this->actingAs($this->admin)
        ->post(route('admin.cars.restore', $car->id))
        ->assertRedirect(route('admin.cars', ['arsip' => 1]));

    expect($car->fresh()->trashed())->toBeFalse();

    $photoPaths = $car->photos()->pluck('path')->all();

    $this->actingAs($this->admin)
        ->delete(route('admin.cars.destroy', $car))
        ->assertRedirect(route('admin.cars'));

    $this->actingAs($this->admin)
        ->get(route('admin.cars', ['arsip' => 1, 'force' => $car->id]))
        ->assertOk()
        ->assertSee('Hapus Permanen');

    $this->actingAs($this->admin)
        ->delete(route('admin.cars.force-destroy', $car->id))
        ->assertRedirect(route('admin.cars', ['arsip' => 1]));

    expect(Car::withTrashed()->find($car->id))->toBeNull();

    foreach ($photoPaths as $photoPath) {
        Storage::disk('public')->assertMissing($photoPath);
    }
});

test('car creation requires the essential fields', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.cars.create'))
        ->post(route('admin.cars.store'), [])
        ->assertRedirect(route('admin.cars.create'))
        ->assertSessionHasErrors([
            'car_model_id',
            'year',
            'price',
            'mileage',
            'transmission',
            'fuel_type',
            'body_type',
            'city',
            'status',
        ]);
});

test('formatted rupiah prices are normalized when creating a car', function () {
    $brand = Brand::factory()->create();
    $carModel = CarModel::factory()->for($brand, 'brand')->create();

    $this->actingAs($this->admin)
        ->post(route('admin.cars.store'), [
            'car_model_id' => $carModel->id,
            'year' => 2021,
            'price' => '215.000.000',
            'mileage' => 12000,
            'transmission' => 'automatic',
            'fuel_type' => 'petrol',
            'body_type' => 'mpv',
            'owner_count' => 1,
            'city' => 'Samarinda',
            'status' => 'available',
        ])
        ->assertRedirect();

    expect(Car::query()->value('price'))->toBe(215000000);
});

test('car photos can be set as primary reordered and deleted', function () {
    Storage::fake('public');

    $car = Car::factory()->create();
    $first = CarPhoto::factory()->for($car, 'car')->primary()->create(['sort_order' => 0, 'path' => 'cars/first.webp']);
    $second = CarPhoto::factory()->for($car, 'car')->create(['sort_order' => 1, 'path' => 'cars/second.webp']);
    $third = CarPhoto::factory()->for($car, 'car')->create(['sort_order' => 2, 'path' => 'cars/third.webp']);

    $this->actingAs($this->admin)
        ->post(route('admin.cars.photos.primary', [$car, $second]))
        ->assertRedirect();

    expect($second->fresh()->is_primary)->toBeTrue()
        ->and($first->fresh()->is_primary)->toBeFalse();

    $this->actingAs($this->admin)
        ->post(route('admin.cars.photos.reorder', $car), [
            'photo_ids' => [$third->id, $second->id, $first->id],
        ])
        ->assertRedirect();

    expect($third->fresh()->sort_order)->toBe(0)
        ->and($second->fresh()->sort_order)->toBe(1)
        ->and($first->fresh()->sort_order)->toBe(2);

    $this->actingAs($this->admin)
        ->delete(route('admin.cars.photos.destroy', [$car, $third]))
        ->assertRedirect();

    expect(CarPhoto::find($third->id))->toBeNull();

    $this->actingAs($this->admin)
        ->delete(route('admin.cars.photos.destroy', [$car, $second]))
        ->assertRedirect();

    expect($first->fresh()->is_primary)->toBeTrue();
});

test('an admin can manage brands car models and features', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.brands.store'), ['name' => 'Honda'])
        ->assertRedirect(route('admin.brands'));

    $brand = Brand::query()->where('name', 'Honda')->firstOrFail();

    expect($brand->slug)->toBe('honda');

    $this->actingAs($this->admin)
        ->put(route('admin.brands.update', $brand), ['name' => 'Honda Motor'])
        ->assertRedirect(route('admin.brands'));

    expect($brand->fresh()->name)->toBe('Honda Motor')
        ->and($brand->fresh()->slug)->toBe('honda');

    $this->actingAs($this->admin)
        ->post(route('admin.car-models.store'), [
            'brand_id' => $brand->id,
            'name' => 'HR-V',
        ])
        ->assertRedirect(route('admin.car-models'));

    $carModel = CarModel::query()->where('name', 'HR-V')->firstOrFail();

    expect($carModel->brand_id)->toBe($brand->id)
        ->and($carModel->slug)->toBe('hr-v');

    $this->actingAs($this->admin)
        ->put(route('admin.car-models.update', $carModel), [
            'brand_id' => $brand->id,
            'name' => 'HR-V Facelift',
        ])
        ->assertRedirect(route('admin.car-models'));

    expect($carModel->fresh()->name)->toBe('HR-V Facelift');

    $this->actingAs($this->admin)
        ->post(route('admin.features.store'), ['name' => 'Kamera belakang'])
        ->assertRedirect(route('admin.features'));

    $feature = Feature::query()->where('name', 'Kamera belakang')->firstOrFail();

    $this->actingAs($this->admin)
        ->put(route('admin.features.update', $feature), ['name' => 'Kamera mundur'])
        ->assertRedirect(route('admin.features'));

    expect($feature->fresh()->name)->toBe('Kamera mundur');

    $this->actingAs($this->admin)
        ->delete(route('admin.features.destroy', $feature))
        ->assertRedirect(route('admin.features'));

    expect(Feature::find($feature->id))->toBeNull();

    $this->actingAs($this->admin)
        ->delete(route('admin.brands.destroy', $brand))
        ->assertRedirect();

    expect($brand->fresh())->not->toBeNull();
});

test('brand index filters brands and keeps the list state after a write', function () {
    $toyota = Brand::factory()->create(['name' => 'Toyota']);
    Brand::factory()->create(['name' => 'Honda']);
    Brand::factory()->create(['name' => 'Mazda']);
    $carModel = CarModel::factory()->for($toyota, 'brand')->create(['name' => 'Avanza']);
    Car::factory()->for($carModel, 'carModel')->create();

    $filters = [
        'q' => 'toyo',
        'coverage' => 'with_models',
        'sort' => 'cars_desc',
    ];

    $this->actingAs($this->admin)
        ->get(route('admin.brands', $filters))
        ->assertOk()
        ->assertSee('Toyota')
        ->assertDontSee('Honda')
        ->assertDontSee('Mazda')
        ->assertSee('Filter aktif:');

    $this->actingAs($this->admin)
        ->post(route('admin.brands.store'), [
            'name' => 'Daihatsu',
            'return_q' => $filters['q'],
            'return_coverage' => $filters['coverage'],
            'return_sort' => $filters['sort'],
            'return_page' => '1',
        ])
        ->assertRedirect(route('admin.brands', $filters + ['page' => '1']));
});

test('an admin can update lead status and export leads to csv', function () {
    $brand = Brand::factory()->create(['name' => 'Suzuki']);
    $carModel = CarModel::factory()->for($brand, 'brand')->create(['name' => 'Ertiga']);
    $car = Car::factory()->for($carModel, 'carModel')->create(['city' => 'Samarinda']);
    $lead = Lead::factory()->for($car, 'car')->create([
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
        'status' => LeadStatus::New,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.leads'))
        ->assertOk()
        ->assertSee('Budi Santoso')
        ->assertSee($lead->phone);

    $this->actingAs($this->admin)
        ->get(route('admin.leads', ['edit' => $lead->id]))
        ->assertOk()
        ->assertSee('Detail Lead')
        ->assertSee('Budi Santoso');

    $this->actingAs($this->admin)
        ->put(route('admin.leads.update', $lead), [
            'status' => 'contacted',
            'notes' => 'Sudah dihubungi, minta kirim foto tambahan.',
        ])
        ->assertRedirect(route('admin.leads'));

    expect($lead->fresh()->status)->toBe(LeadStatus::Contacted)
        ->and($lead->fresh()->notes)->toBe('Sudah dihubungi, minta kirim foto tambahan.');

    $response = $this->actingAs($this->admin)->get(route('admin.leads.export'));

    $response->assertOk();
    expect($response->streamedContent())
        ->toContain('Budi Santoso')
        ->toContain('081234567890')
        ->toContain('contacted');
});

test('an admin can update site settings', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.settings'))
        ->assertOk()
        ->assertSee('Pengaturan Situs');

    $this->actingAs($this->admin)
        ->put(route('admin.settings.update'), [
            'settings' => [
                'site_name' => 'MobilTa',
                'whatsapp_number' => '6281234567890',
                'phone_number' => '081234567890',
                'email' => 'halo@mobilta.test',
                'address' => 'Jl. Ahmad Yani No. 88, Samarinda',
                'opening_hours' => 'Senin - Sabtu, 08.00 - 17.00 WITA',
                'map_embed' => 'https://maps.google.com/embed',
                'hero_title' => 'Temukan mobil impianmu',
                'hero_subtitle' => 'Mobil berkualitas dengan harga transparan.',
            ],
        ])
        ->assertRedirect();

    expect(Setting::query()->where('key', 'whatsapp_number')->value('value'))->toBe('6281234567890')
        ->and(Setting::query()->where('key', 'opening_hours')->value('value'))->toBe('Senin - Sabtu, 08.00 - 17.00 WITA');
});

test('an admin can manage pages and faqs', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.pages.create'))
        ->assertOk()
        ->assertSee('Tambah Halaman');

    $this->actingAs($this->admin)
        ->post(route('admin.pages.store'), [
            'title' => 'Tentang Kami',
            'content' => 'Konten tentang showroom.',
            'meta_title' => 'Tentang MobilTa',
            'meta_description' => 'Kenali showroom kami.',
            'is_published' => '1',
        ])
        ->assertRedirect(route('admin.pages'));

    $page = Page::query()->where('title', 'Tentang Kami')->firstOrFail();

    expect($page->slug)->toBe('tentang-kami')
        ->and($page->is_published)->toBeTrue();

    $this->actingAs($this->admin)
        ->put(route('admin.pages.update', $page), [
            'title' => 'Tentang Showroom',
            'content' => 'Konten terbaru.',
            'meta_title' => 'Tentang Showroom',
            'meta_description' => 'Deskripsi terbaru.',
        ])
        ->assertRedirect(route('admin.pages'));

    expect($page->fresh()->title)->toBe('Tentang Showroom')
        ->and($page->fresh()->is_published)->toBeFalse();

    $this->actingAs($this->admin)
        ->post(route('admin.faqs.store'), [
            'question' => 'Apakah bisa test drive?',
            'answer' => 'Bisa, silakan buat janji terlebih dahulu.',
            'sort_order' => 1,
            'is_published' => '1',
        ])
        ->assertRedirect(route('admin.faqs'));

    $faq = Faq::query()->where('question', 'Apakah bisa test drive?')->firstOrFail();

    $this->actingAs($this->admin)
        ->put(route('admin.faqs.update', $faq), [
            'question' => 'Apakah tersedia test drive?',
            'answer' => 'Tersedia dengan janji terlebih dahulu.',
            'sort_order' => 2,
        ])
        ->assertRedirect(route('admin.faqs'));

    expect($faq->fresh()->question)->toBe('Apakah tersedia test drive?')
        ->and($faq->fresh()->is_published)->toBeFalse();

    $this->actingAs($this->admin)
        ->delete(route('admin.pages.destroy', $page))
        ->assertRedirect(route('admin.pages'));

    $this->actingAs($this->admin)
        ->delete(route('admin.faqs.destroy', $faq))
        ->assertRedirect(route('admin.faqs'));

    expect(Page::find($page->id))->toBeNull()
        ->and(Faq::find($faq->id))->toBeNull();
});
