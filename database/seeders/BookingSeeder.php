<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Terrain;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $terrains = Terrain::all();
        $users = User::all();

        // Create bookings
        $terrains->each(function ($terrain) use ($users) {
            // Each terrain gets 0-3 bookings
            $bookingCount = rand(0, 3);
            
            for ($i = 0; $i < $bookingCount; $i++) {
                $renter = $users->where('id', '!=', $terrain->owner_id)->random();
                
                Booking::factory()->create([
                    'terrain_id' => $terrain->id,
                    'renter_id' => $renter->id,
                ]);
            }
        });
    }
}