<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarPhoto>
 */
class CarPhotoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'path' => 'cars/'.fake()->uuid().'.webp',
            'sort_order' => 0,
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
