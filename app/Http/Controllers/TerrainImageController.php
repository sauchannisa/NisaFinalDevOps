<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTerrainImageRequest;
use App\Http\Requests\UpdateTerrainImageRequest;
use App\Models\TerrainImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TerrainImageController extends Controller
{
    public function index(Request $request)
    {
        $query = TerrainImage::with('terrain');

        // Filter by terrain
        if ($request->filled('terrain_id')) {
            $query->where('terrain_id', $request->terrain_id);
        }

        $images = $query->orderBy('uploaded_at', 'desc')->paginate(20);

        return response()->json([
            'data' => $images->items(),
            'meta' => [
                'current_page' => $images->currentPage(),
                'last_page' => $images->lastPage(),
                'per_page' => $images->perPage(),
                'total' => $images->total(),
            ],
        ]);
    }

    public function store(StoreTerrainImageRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('terrain-images', 'public');
        }

        $data['uploaded_at'] = now();

        $image = TerrainImage::create($data);
        $image->load('terrain');

        return response()->json([
            'message' => 'Image uploaded successfully',
            'data' => $image,
        ], 201);
    }

    public function show(TerrainImage $terrainImage)
    {
        $terrainImage->load('terrain');

        return response()->json([
            'data' => $terrainImage,
        ]);
    }

    public function update(UpdateTerrainImageRequest $request, TerrainImage $terrainImage)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image_path')) {
            // Delete old image
            if ($terrainImage->image_path) {
                Storage::disk('public')->delete($terrainImage->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('terrain-images', 'public');
        }

        $terrainImage->update($data);
        $terrainImage->load('terrain');

        return response()->json([
            'message' => 'Image updated successfully',
            'data' => $terrainImage,
        ]);
    }

    public function destroy(TerrainImage $terrainImage)
    {
        // Delete image file
        if ($terrainImage->image_path) {
            Storage::disk('public')->delete($terrainImage->image_path);
        }

        $terrainImage->delete();

        return response()->json([
            'message' => 'Image deleted successfully',
        ]);
    }
}