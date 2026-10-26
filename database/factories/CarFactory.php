<?php

namespace Database\Factories;

use App\Enums\BodyType;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Car;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_model_id' => CarModel::factory(),
            'variant' => fake()->optional(0.7)->randomElement(['G', 'V', 'E', 'Sport', 'Type R']),
            'year' => fake()->numberBetween(2010, (int) date('Y')),
            'price' => fake()->numberBetween(80_000_000, 850_000_000),
            'mileage' => fake()->numberBetween(5_000, 180_000),
            'transmission' => fake()->randomElement(Transmission::cases()),
            'fuel_type' => fake()->randomElement(FuelType::cases()),
            'engine_capacity' => fake()->randomElement([1000, 1200, 1500, 2000, 2400, 3000]),
            'body_type' => fake()->randomElement(BodyType::cases()),
            'color' => fake()->randomElement(['Hitam', 'Putih', 'Silver', 'Abu-abu', 'Merah', 'Biru']),
            'owner_count' => fake()->numberBetween(1, 3),
            'plate_area' => fake()->randomElement(['KT', 'KN', 'KU', 'DH', 'B', 'D', 'L', 'N']),
            'tax_expiry' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'document_completeness' => fake()->randomElement(['Lengkap', 'BPKB saja', 'STNK saja']),
            'condition' => fake()->sentence(12),
            'description' => fake()->paragraphs(2, true),
            'city' => fake()->randomElement(['Balikpapan', 'Samarinda', 'Banjarmasin', 'Makassar', 'Tarakan', 'Pontianak']),
            'status' => CarStatus::Available,
            'is_featured' => fake()->boolean(20),
            'views_count' => fake()->numberBetween(0, 500),
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::Available,
        ]);
    }

    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::Reserved,
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CarStatus::Sold,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
