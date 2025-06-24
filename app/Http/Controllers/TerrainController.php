<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTerrainRequest;
use App\Http\Requests\UpdateTerrainRequest;
use App\Models\Terrain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TerrainController extends Controller
{
    public function __construct()
    {
        // No authentication middleware for simplified version
    }

    public function index(Request $request)
    {
        $query = Terrain::with(['owner', 'images', 'reviews'])
            ->where('is_available', true);

        // Search filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        if ($request->filled('min_area')) {
            $query->where('area_size', '>=', $request->min_area);
        }

        if ($request->filled('max_area')) {
            $query->where('area_size', '<=', $request->max_area);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $validSortColumns = ['created_at', 'price_per_day', 'area_size', 'title'];
        if (in_array($sortBy, $validSortColumns)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $terrains = $query->paginate(12);

        return response()->json([
            'data' => $terrains->items(),
            'meta' => [
                'current_page' => $terrains->currentPage(),
                'last_page' => $terrains->lastPage(),
                'per_page' => $terrains->perPage(),
                'total' => $terrains->total(),
            ],
        ]);
    }

    public function store(StoreTerrainRequest $request)
    {
        $data = $request->validated();

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('terrain-images', 'public');
        }

        $terrain = Terrain::create($data);
        $terrain->load(['owner', 'images', 'reviews']);

        return response()->json([
            'message' => 'Terrain created successfully',
            'data' => $terrain,
        ], 201);
    }

    public function show(Terrain $terrain)
    {
        $terrain->load([
            'owner',
            'images',
            'reviews.user',
            'bookings' => function ($query) {
                $query->whereIn('status', ['pending', 'approved'])
                      ->select('terrain_id', 'start_date', 'end_date', 'status');
            }
        ]);

        // Add average rating and review count
        $terrain->average_rating = $terrain->averageRating();
        $terrain->total_reviews = $terrain->totalReviews();

        return response()->json([
            'data' => $terrain,
        ]);
    }

    public function update(UpdateTerrainRequest $request, Terrain $terrain)
    {
        $data = $request->validated();

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            // Delete old image if exists
            if ($terrain->main_image) {
                Storage::disk('public')->delete($terrain->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('terrain-images', 'public');
        }

        $terrain->update($data);
        $terrain->load(['owner', 'images', 'reviews']);

        return response()->json([
            'message' => 'Terrain updated successfully',
            'data' => $terrain,
        ]);
    }

    public function destroy(Terrain $terrain)
    {
        // Delete main image if exists
        if ($terrain->main_image) {
            Storage::disk('public')->delete($terrain->main_image);
        }

        // Delete all associated images
        foreach ($terrain->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $terrain->delete();

        return response()->json([
            'message' => 'Terrain deleted successfully',
        ]);
    }

    public function myTerrains(Request $request)
    {
        $ownerId = $request->get('owner_id', 1); // Default to user ID 1
        
        $terrains = Terrain::with(['images', 'reviews', 'bookings'])
            ->where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => $terrains->items(),
            'meta' => [
                'current_page' => $terrains->currentPage(),
                'last_page' => $terrains->lastPage(),
                'per_page' => $terrains->perPage(),
                'total' => $terrains->total(),
            ],
        ]);
    }
}