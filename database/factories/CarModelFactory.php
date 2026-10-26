<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CarModel>
 */
class CarModelFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(2, true));

        return [
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
