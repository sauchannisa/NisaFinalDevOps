<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'status' => 'sometimes|required|in:pending,approved,rejected,cancelled,completed',
            'total_price' => 'sometimes|required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'Please select a start date.',
            'end_date.required' => 'Please select an end date.',
            'end_date.after' => 'End date must be after the start date.',
            'status.in' => 'Invalid status selected.',
            'total_price.numeric' => 'Total price must be a valid amount.',
            'total_price.min' => 'Total price cannot be negative.',
        ];
    }
}