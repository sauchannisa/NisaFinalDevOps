<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Booking $booking): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, Booking $booking): bool
    {
        return true;
    }

    public function delete(?User $user, Booking $booking): bool
    {
        return true;
    }

    public function restore(?User $user, Booking $booking): bool
    {
        return true;
    }

    public function forceDelete(?User $user, Booking $booking): bool
    {
        return true;
    }

    public function approve(?User $user, Booking $booking): bool
    {
        return true;
    }

    public function reject(?User $user, Booking $booking): bool
    {
        return true;
    }

    public function complete(?User $user, Booking $booking): bool
    {
        return true;
    }
}