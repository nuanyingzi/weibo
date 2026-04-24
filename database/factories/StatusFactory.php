<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date_time = $this->faker->date . ' ' . $this->faker->time;
        return [
            //
            'user_id' => $this->faker->randomElement(range(1, 10)),
            'content' => $this->faker->text(600),
            'created_at' => $date_time,
            'updated_at' => $date_time,
        ];
    }
}
