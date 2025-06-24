<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Terrain;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $completedBookings = Booking::where('status', 'completed')->get();

        // Create reviews for completed bookings
        $completedBookings->each(function ($booking) {
            // 70% chance of getting a review
            if (rand(1, 100) <= 70) {
                Review::factory()->create([
                    'terrain_id' => $booking->terrain_id,
                    'user_id' => $booking->renter_id,
                ]);
            }
        });

        // Create some additional reviews
        Review::factory(15)->create();
    }
}