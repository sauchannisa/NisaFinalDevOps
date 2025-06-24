<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Terrain;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-3 months', '+3 months');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d') . ' +2 weeks');
        $days = $startDate->diff($endDate)->days + 1;
        $pricePerDay = $this->faker->randomFloat(2, 50, 500);
        
        return [
            'terrain_id' => Terrain::factory(),
            'renter_id' => User::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $pricePerDay * $days,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'cancelled', 'completed']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}