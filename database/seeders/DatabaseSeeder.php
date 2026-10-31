<?php

namespace Database\Seeders;

use App\Enums\BodyType;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarPhoto;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\Setting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => 'password123',
            ],
        );

        if (Car::query()->exists()) {
            $this->addDummyGalleryPhotos();

            return;
        }

        $models = [
            ['Toyota', 'Avanza', 2019, 218_000_000, 52_000, BodyType::Mpv, Transmission::Automatic, FuelType::Petrol, 'toyota-avanza'],
            ['Honda', 'HR-V', 2022, 338_000_000, 28_000, BodyType::Suv, Transmission::Automatic, FuelType::Petrol, 'honda-hrv'],
            ['Mitsubishi', 'Xpander', 2021, 245_000_000, 41_000, BodyType::Mpv, Transmission::Automatic, FuelType::Petrol, 'mitsubishi-xpander'],
            ['Toyota', 'Innova Zenix', 2023, 468_000_000, 18_000, BodyType::Mpv, Transmission::Automatic, FuelType::Hybrid, 'toyota-innova-zenix'],
            ['Honda', 'Brio', 2022, 176_000_000, 31_000, BodyType::Hatchback, Transmission::Automatic, FuelType::Petrol, 'honda-brio'],
            ['Toyota', 'Fortuner', 2020, 495_000_000, 48_000, BodyType::Suv, Transmission::Automatic, FuelType::Diesel, 'toyota-fortuner'],
            ['Suzuki', 'Ertiga', 2020, 189_000_000, 62_000, BodyType::Mpv, Transmission::Automatic, FuelType::Petrol, 'suzuki-ertiga'],
            ['Honda', 'BR-V', 2021, 239_000_000, 37_000, BodyType::Suv, Transmission::Automatic, FuelType::Petrol, 'honda-brv'],
        ];

        foreach ($models as [$brandName, $modelName, $year, $price, $mileage, $bodyType, $transmission, $fuelType, $image]) {
            $brand = Brand::firstOrCreate(['name' => $brandName]);
            $model = CarModel::firstOrCreate(['brand_id' => $brand->id, 'name' => $modelName]);
            $car = Car::create([
                'car_model_id' => $model->id,
                'variant' => $modelName === 'Brio' ? 'RS' : 'Signature',
                'year' => $year,
                'price' => $price,
                'mileage' => $mileage,
                'transmission' => $transmission,
                'fuel_type' => $fuelType,
                'engine_capacity' => 1500,
                'body_type' => $bodyType,
                'color' => 'Putih',
                'owner_count' => 1,
                'plate_area' => 'KT',
                'tax_expiry' => now()->addMonths(8),
                'document_completeness' => 'Lengkap',
                'condition' => 'Terawat, siap pakai, dan sudah melalui inspeksi showroom.',
                'description' => 'Unit pilihan RodaTa dengan riwayat perawatan yang jelas.',
                'status' => CarStatus::Available,
                'is_featured' => in_array($modelName, ['Avanza', 'HR-V', 'Xpander', 'Innova Zenix'], true),
            ]);
            $car->features()->sync(
                Feature::query()
                    ->whereIn('name', ['Bebas banjir', 'Surat lengkap', 'Siap test drive'])
                    ->pluck('id')
            );
            foreach (['cars/'.$image.'.webp', 'cars/inspection.webp', 'cars/hero-showroom.webp'] as $sortOrder => $path) {
                CarPhoto::create([
                    'car_id' => $car->id,
                    'path' => $path,
                    'sort_order' => $sortOrder,
                    'is_primary' => $sortOrder === 0,
                ]);
            }
        }

        foreach (['Bebas banjir', 'Bebas tabrak', 'Surat lengkap', 'Siap test drive'] as $feature) {
            Feature::firstOrCreate(['name' => $feature]);
        }

        foreach ([
            ['question' => 'Apakah bisa test drive?', 'answer' => 'Bisa. Silakan buat janji melalui WhatsApp agar unit kami siapkan.'],
            ['question' => 'Apakah harga masih bisa nego?', 'answer' => 'Harga kami transparan dan kompetitif. Tim kami siap membantu mencari skema terbaik.'],
            ['question' => 'Apakah tersedia tukar tambah?', 'answer' => 'Tersedia untuk unit dan wilayah tertentu. Hubungi tim kami untuk penilaian awal.'],
        ] as $faq) {
            Faq::firstOrCreate($faq);
        }

        foreach ([
            'whatsapp_number' => '6281234567890',
            'phone_number' => '081234567890',
            'address' => 'Jl. Ahmad Yani No. 88, Samarinda',
        ] as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /**
     * Add gallery placeholders to existing demo cars without touching uploaded photos.
     */
    private function addDummyGalleryPhotos(): void
    {
        foreach (Car::query()->with('photos')->get() as $car) {
            if ($car->photos->count() >= 3) {
                continue;
            }

            $paths = ['cars/inspection.webp', 'cars/hero-showroom.webp'];
            $sortOrder = (int) $car->photos->max('sort_order');

            foreach ($paths as $path) {
                if ($car->photos->contains('path', $path)) {
                    continue;
                }

                CarPhoto::create([
                    'car_id' => $car->id,
                    'path' => $path,
                    'sort_order' => ++$sortOrder,
                    'is_primary' => false,
                ]);

                if ($car->photos->count() + 1 >= 3) {
                    break;
                }
            }
        }
    }
}
