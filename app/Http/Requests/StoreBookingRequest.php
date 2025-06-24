<?php

namespace App\Http\Requests;

use App\Models\Terrain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'terrain_id' => 'required|exists:terrains,id',
            'renter_id' => 'required|exists:users,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'terrain_id.required' => 'Please select a terrain to book.',
            'terrain_id.exists' => 'The selected terrain is not available for booking.',
            'start_date.required' => 'Please select a start date.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.required' => 'Please select an end date.',
            'end_date.after' => 'End date must be after the start date.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->terrain_id && $this->start_date && $this->end_date) {
                $terrain = Terrain::find($this->terrain_id);
                
                if ($terrain) {
                    // Check if dates are within terrain availability
                    if ($terrain->available_from && $this->start_date < $terrain->available_from) {
                        $validator->errors()->add('start_date', 'Start date is before terrain availability period.');
                    }
                    
                    if ($terrain->available_to && $this->end_date > $terrain->available_to) {
                        $validator->errors()->add('end_date', 'End date is after terrain availability period.');
                    }
                    
                    // Check for conflicting bookings
                    $conflictingBooking = $terrain->bookings()
                        ->whereIn('status', ['pending', 'approved'])
                        ->where(function ($query) {
                            $query->whereBetween('start_date', [$this->start_date, $this->end_date])
                                  ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                                  ->orWhere(function ($q) {
                                      $q->where('start_date', '<=', $this->start_date)
                                        ->where('end_date', '>=', $this->end_date);
                                  });
                        })
                        ->exists();
                    
                    if ($conflictingBooking) {
                        $validator->errors()->add('start_date', 'The selected dates conflict with existing bookings.');
                    }
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'renter_id' => $this->input('renter_id', 1), // Default to user ID 1 for now
            'status' => 'pending',
        ]);
        
        // Calculate total price if terrain exists
        if ($this->terrain_id && $this->start_date && $this->end_date) {
            $terrain = Terrain::find($this->terrain_id);
            if ($terrain) {
                $startDate = \Carbon\Carbon::parse($this->start_date);
                $endDate = \Carbon\Carbon::parse($this->end_date);
                $days = $startDate->diffInDays($endDate) + 1;
                $this->merge([
                    'total_price' => $terrain->price_per_day * $days,
                ]);
            }
        }
    }
}