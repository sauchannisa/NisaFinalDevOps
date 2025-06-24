<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'sometimes|required|string|max:255',
            'amount_paid' => 'sometimes|required|numeric|min:0.01',
            'status' => 'sometimes|required|in:paid,failed,refunded',
            'transaction_id' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please select a payment method.',
            'amount_paid.required' => 'Please enter the payment amount.',
            'amount_paid.numeric' => 'Payment amount must be a valid number.',
            'amount_paid.min' => 'Payment amount must be greater than 0.',
            'status.in' => 'Invalid payment status.',
        ];
    }
}