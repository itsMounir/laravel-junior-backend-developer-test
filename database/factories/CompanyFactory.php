<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber,
            'phone_verified_at' => now(),
            'owner_id' => fake()->numberBetween(1,10),
            'country_id' => fake()->numberBetween(1,10),
            'industry_id' => fake()->numberBetween(1,10),
        ];
    }
}
