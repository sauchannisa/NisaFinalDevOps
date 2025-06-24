<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTerrainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'location' => 'required|string|max:255',
            'area_size' => 'required|numeric|min:1|max:999999.99',
            'price_per_day' => 'required|numeric|min:0.01|max:999999.99',
            'available_from' => 'nullable|date|after_or_equal:today',
            'available_to' => 'nullable|date|after:available_from',
            'is_available' => 'boolean',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your terrain.',
            'location.required' => 'Please specify the location of your terrain.',
            'area_size.required' => 'Please specify the area size.',
            'area_size.numeric' => 'Area size must be a valid number.',
            'price_per_day.required' => 'Please set a daily price.',
            'price_per_day.numeric' => 'Price must be a valid amount.',
            'available_from.after_or_equal' => 'Availability start date cannot be in the past.',
            'available_to.after' => 'Availability end date must be after the start date.',
            'main_image.image' => 'The uploaded file must be an image.',
            'main_image.max' => 'The image size cannot exceed 2MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'owner_id' => $this->input('owner_id', 1), // Default to user ID 1 for now
            'is_available' => $this->boolean('is_available', true),
        ]);
    }
}