<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InsuranceCompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'insurance_name' => $this->faker->unique()->company . ' 保険',
            'insurance_type' => $this->faker->randomElement(['1', '2']),
            'contactname' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'contactname2' => $this->faker->optional()->name(),
            'phone_number2' => $this->faker->optional()->phoneNumber(),
            'email2' => $this->faker->optional()->safeEmail(),
            'importantnotes' => $this->faker->optional()->sentence(),
        ];
    }
}
