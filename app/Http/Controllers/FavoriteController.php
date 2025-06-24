<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $query = Favorite::with(['terrain.owner', 'user']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by terrain
        if ($request->filled('terrain_id')) {
            $query->where('terrain_id', $request->terrain_id);
        }

        $favorites = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'data' => $favorites->items(),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
            ],
        ]);
    }

    public function store(StoreFavoriteRequest $request)
    {
        // Check if already favorited
        $existingFavorite = Favorite::where('user_id', $request->user_id)
            ->where('terrain_id', $request->terrain_id)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'message' => 'Terrain is already in favorites',
                'data' => $existingFavorite->load(['terrain.owner', 'user']),
            ], 409);
        }

        $favorite = Favorite::create($request->validated());
        $favorite->load(['terrain.owner', 'user']);

        return response()->json([
            'message' => 'Terrain added to favorites',
            'data' => $favorite,
        ], 201);
    }

    public function show(Favorite $favorite)
    {
        $favorite->load(['terrain.owner', 'user']);

        return response()->json([
            'data' => $favorite,
        ]);
    }

    public function update(UpdateFavoriteRequest $request, Favorite $favorite)
    {
        $favorite->update($request->validated());
        $favorite->load(['terrain.owner', 'user']);

        return response()->json([
            'message' => 'Favorite updated successfully',
            'data' => $favorite,
        ]);
    }

    public function destroy(Favorite $favorite)
    {
        $favorite->delete();

        return response()->json([
            'message' => 'Terrain removed from favorites',
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'terrain_id' => 'required|exists:terrains,id',
        ]);

        $favorite = Favorite::where('user_id', $request->user_id)
            ->where('terrain_id', $request->terrain_id)
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            return response()->json([
                'message' => 'Terrain removed from favorites',
                'is_favorited' => false,
            ]);
        } else {
            // Add to favorites
            $favorite = Favorite::create([
                'user_id' => $request->user_id,
                'terrain_id' => $request->terrain_id,
            ]);
            $favorite->load(['terrain.owner', 'user']);

            return response()->json([
                'message' => 'Terrain added to favorites',
                'is_favorited' => true,
                'data' => $favorite,
            ], 201);
        }
    }

    public function getUserFavorites(Request $request)
    {
        $userId = $request->get('user_id', 1); // Default to user ID 1
        
        $favorites = Favorite::with(['terrain.owner', 'terrain.images', 'terrain.reviews'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json([
            'data' => $favorites->items(),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
            ],
        ]);
    }
}