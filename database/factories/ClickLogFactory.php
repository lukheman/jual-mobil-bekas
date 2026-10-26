<?php

namespace Database\Factories;

use App\Enums\ClickType;
use App\Models\Car;
use App\Models\ClickLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClickLog>
 */
class ClickLogFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'type' => ClickType::Whatsapp,
            'created_at' => now(),
        ];
    }

    public function whatsapp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ClickType::Whatsapp,
        ]);
    }

    public function phone(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ClickType::Phone,
        ]);
    }
}
