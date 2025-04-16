<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_name' => '会議室' . $this->faker->unique()->randomDigitNotZero(),
            'calendar_id' => $this->faker->unique()->uuid(),
            'office_id' => $this->faker->numberBetween(1, 6),
            'importantnotes' => $this->faker->optional()->sentence(),
        ];
    }
}