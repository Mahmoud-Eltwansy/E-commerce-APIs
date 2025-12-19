<?php

namespace Database\Factories;

use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'en' => fake('en')->realText(10),
                'ar' => fake('ar_EG')->realText(10)
            ],
            'description' => [
                'en' => fake('en')->realText(100),
                'ar' => fake('ar_EG')->realText(100)
            ],
            'price' => fake()->randomFloat(2, 100, 10000),
            'quantity' => fake()->numberBetween(10, 200)
        ];
    }
}
