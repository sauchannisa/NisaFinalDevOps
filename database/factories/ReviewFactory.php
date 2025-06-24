<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Terrain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'terrain_id' => Terrain::factory(),
            'user_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->paragraph(2),
        ];
    }

    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 5,
            'comment' => $this->faker->randomElement([
                'Excellent terrain! Perfect for our event.',
                'Amazing location and great facilities.',
                'Highly recommended! Will definitely book again.',
            ]),
        ]);
    }

    public function poor(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
            'comment' => $this->faker->randomElement([
                'Not as described. Had some issues.',
                'Could be better maintained.',
                'Had some problems during our stay.',
            ]),
        ]);
    }
}