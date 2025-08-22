<?php

namespace Database\Factories;

use Biigle\Project;
use Biigle\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $endDate = $this->faker->dateTimeBetween($startDate, '+2 years');
        
        return [
            'name' => $this->faker->words(3, true) . ' Budget',
            'description' => $this->faker->optional()->sentence(),
            'amount' => $this->faker->randomFloat(2, 1000, 100000),
            'spent' => $this->faker->randomFloat(2, 0, 50000),
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'JPY', 'CAD']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'active' => $this->faker->boolean(80), // 80% chance of being active
            'project_id' => Project::factory(),
            'creator_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the budget is active and current.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => true,
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonth(),
            ];
        });
    }

    /**
     * Indicate that the budget is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }

    /**
     * Indicate that the budget is overrun.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function overrun()
    {
        return $this->state(function (array $attributes) {
            $amount = $attributes['amount'] ?? 10000;
            return [
                'spent' => $amount * 1.2, // 20% over budget
            ];
        });
    }
}