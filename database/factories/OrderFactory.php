<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [


            'client_id' => fake()->boolean(10)
                ? null : fake()->numberBetween(1, 7),
            'description' => fake()->sentence(),
            'tracking' => 'TRK' . fake()->unique()->numerify('########'),
            'origin' => 'Portugal',
            'destination' => 'Moçambique',
            'reception_date' => fake()->date(),
            'service_type' => fake()->randomElement([
                'express',
                'normal'
            ]),
            'volume_number' => fake()->numberBetween(1, 10),
            'weight' => fake()->randomFloat(2, 1, 50),
            'declared_weight' => fake()->randomFloat(2, 1, 50),
            'category_id' => fake()->randomElement([
                1,
                2
            ]),
            'responsible_id' => 2,
            'invoice_id' => null,
            'store_id' => fake()->numberBetween(1, 5),
        ];
    }
}
