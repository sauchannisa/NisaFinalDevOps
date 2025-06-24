<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct()
    {
        // No authentication middleware for simplified version
    }

    public function index(Request $request)
    {
        $query = Booking::with(['terrain.owner', 'renter', 'payments']);

        // Filter by user (renter or owner)
        if ($request->filled('user_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('renter_id', $request->user_id)
                  ->orWhereHas('terrain', function ($terrainQuery) use ($request) {
                      $terrainQuery->where('owner_id', $request->user_id);
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role (as renter or owner)
        if ($request->filled('role') && $request->filled('user_id')) {
            if ($request->role === 'renter') {
                $query->where('renter_id', $request->user_id);
            } elseif ($request->role === 'owner') {
                $query->whereHas('terrain', function ($terrainQuery) use ($request) {
                    $terrainQuery->where('owner_id', $request->user_id);
                });
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'data' => $bookings->items(),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::create($request->validated());
        $booking->load(['terrain.owner', 'renter']);

        return response()->json([
            'message' => 'Booking request submitted successfully',
            'data' => $booking,
        ], 201);
    }

    public function show(Booking $booking)
    {
        $booking->load(['terrain.owner', 'renter', 'payments']);

        return response()->json([
            'data' => $booking,
        ]);
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $booking->update($request->validated());
        $booking->load(['terrain.owner', 'renter', 'payments']);

        return response()->json([
            'message' => 'Booking updated successfully',
            'data' => $booking,
        ]);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return response()->json([
            'message' => 'Booking cancelled successfully',
        ]);
    }

    public function approve(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending bookings can be approved',
            ], 422);
        }

        $booking->update(['status' => 'approved']);
        $booking->load(['terrain.owner', 'renter']);

        return response()->json([
            'message' => 'Booking approved successfully',
            'data' => $booking,
        ]);
    }

    public function reject(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending bookings can be rejected',
            ], 422);
        }

        $booking->update(['status' => 'rejected']);
        $booking->load(['terrain.owner', 'renter']);

        return response()->json([
            'message' => 'Booking rejected',
            'data' => $booking,
        ]);
    }

    public function complete(Booking $booking)
    {
        if ($booking->status !== 'approved') {
            return response()->json([
                'message' => 'Only approved bookings can be completed',
            ], 422);
        }

        $booking->update(['status' => 'completed']);
        $booking->load(['terrain.owner', 'renter']);

        return response()->json([
            'message' => 'Booking marked as completed',
            'data' => $booking,
        ]);
    }
}