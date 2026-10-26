<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Models\Car;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'name' => fake()->name(),
            'phone' => '08'.fake()->numerify('##########'),
            'email' => fake()->safeEmail(),
            'type' => LeadType::Inquiry,
            'preferred_schedule' => null,
            'message' => fake()->sentence(),
            'status' => LeadStatus::New,
            'notes' => null,
            'consent_at' => now(),
        ];
    }

    public function withoutCar(): static
    {
        return $this->state(fn (array $attributes) => [
            'car_id' => null,
        ]);
    }

    public function testDrive(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LeadType::TestDrive,
            'preferred_schedule' => now()->addDay(),
        ]);
    }

    public function contacted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::Contacted,
        ]);
    }

    public function deal(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::Deal,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::Cancelled,
        ]);
    }
}
