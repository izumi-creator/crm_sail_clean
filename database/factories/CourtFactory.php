<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourtFactory extends Factory
{
    public function definition(): array
    {
        return [
            'court_name' => $this->faker->unique()->company . ' 裁判所',
            'court_type' => $this->faker->randomElement(['1', '2', '3', '4', '5']),
            'postal_code' => $this->faker->postcode(),
            'location' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'importantnotes' => $this->faker->optional()->sentence(),
        ];
    }
}