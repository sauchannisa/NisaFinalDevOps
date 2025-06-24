<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string|max:255',
            'amount_paid' => 'required|numeric|min:0.01',
            'status' => 'sometimes|in:paid,failed,refunded',
            'transaction_id' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'Please select a booking.',
            'booking_id.exists' => 'The selected booking does not exist.',
            'payment_method.required' => 'Please select a payment method.',
            'amount_paid.required' => 'Please enter the payment amount.',
            'amount_paid.numeric' => 'Payment amount must be a valid number.',
            'amount_paid.min' => 'Payment amount must be greater than 0.',
            'status.in' => 'Invalid payment status.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'paid'),
        ]);
    }
}