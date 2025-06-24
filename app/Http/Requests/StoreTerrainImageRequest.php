<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTerrainImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'terrain_id' => 'required|exists:terrains,id',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'terrain_id.required' => 'Please select a terrain.',
            'terrain_id.exists' => 'The selected terrain does not exist.',
            'image_path.required' => 'Please select an image to upload.',
            'image_path.image' => 'The uploaded file must be an image.',
            'image_path.max' => 'The image size cannot exceed 2MB.',
        ];
    }
}