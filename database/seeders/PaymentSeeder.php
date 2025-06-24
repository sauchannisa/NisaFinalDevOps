<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = Booking::whereIn('status', ['approved', 'completed'])->get();

        $bookings->each(function ($booking) {
            // Each approved/completed booking gets a payment
            Payment::factory()->create([
                'booking_id' => $booking->id,
                'amount_paid' => $booking->total_price,
            ]);
        });

        // Create some additional payments for testing
        Payment::factory(10)->create();
    }
}