<?php

namespace Database\Factories;

use App\Models\Terrain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TerrainFactory extends Factory
{
    protected $model = Terrain::class;

    public function definition(): array
    {
        $availableFrom = $this->faker->dateTimeBetween('now', '+1 month');
        $availableTo = $this->faker->dateTimeBetween($availableFrom, '+6 months');

        return [
            'owner_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'location' => $this->faker->address(),
            'area_size' => $this->faker->randomFloat(2, 100, 10000), // 100 to 10,000 sqm
            'price_per_day' => $this->faker->randomFloat(2, 50, 500), // $50 to $500 per day
            'available_from' => $availableFrom,
            'available_to' => $availableTo,
            'is_available' => $this->faker->boolean(80), // 80% chance of being available
            'main_image' => $this->faker->imageUrl(800, 600, 'nature', true, 'terrain'),
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
        ]);
    }
}