<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('booking.terrain', 'booking.renter');

        // Filter by booking
        if ($request->filled('booking_id')) {
            $query->where('booking_id', $request->booking_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        return response()->json([
            'data' => $payments->items(),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();
        $data['payment_date'] = now();

        $payment = Payment::create($data);
        $payment->load('booking.terrain', 'booking.renter');

        return response()->json([
            'message' => 'Payment recorded successfully',
            'data' => $payment,
        ], 201);
    }

    public function show(Payment $payment)
    {
        $payment->load('booking.terrain', 'booking.renter');

        return response()->json([
            'data' => $payment,
        ]);
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());
        $payment->load('booking.terrain', 'booking.renter');

        return response()->json([
            'message' => 'Payment updated successfully',
            'data' => $payment,
        ]);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully',
        ]);
    }

    public function refund(Payment $payment)
    {
        if ($payment->status !== 'paid') {
            return response()->json([
                'message' => 'Only paid payments can be refunded',
            ], 422);
        }

        $payment->update(['status' => 'refunded']);
        $payment->load('booking.terrain', 'booking.renter');

        return response()->json([
            'message' => 'Payment refunded successfully',
            'data' => $payment,
        ]);
    }
}