<?php

namespace Database\Factories;

use Biigle\Volume;
use Illuminate\Database\Eloquent\Factories\Factory;

class LabelSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->username(),
            'description' => $this->faker->sentence(),
        ];
    }
}
