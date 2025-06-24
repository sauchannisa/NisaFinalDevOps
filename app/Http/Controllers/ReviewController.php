<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['terrain', 'user']);

        // Filter by terrain
        if ($request->filled('terrain_id')) {
            $query->where('terrain_id', $request->terrain_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by minimum rating
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $validSortColumns = ['created_at', 'rating'];
        if (in_array($sortBy, $validSortColumns)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $reviews = $query->paginate(15);

        return response()->json([
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    public function store(StoreReviewRequest $request)
    {
        $review = Review::create($request->validated());
        $review->load(['terrain', 'user']);

        return response()->json([
            'message' => 'Review submitted successfully',
            'data' => $review,
        ], 201);
    }

    public function show(Review $review)
    {
        $review->load(['terrain', 'user']);

        return response()->json([
            'data' => $review,
        ]);
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $review->update($request->validated());
        $review->load(['terrain', 'user']);

        return response()->json([
            'message' => 'Review updated successfully',
            'data' => $review,
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }

    public function getTerrainStats(Request $request)
    {
        $terrainId = $request->get('terrain_id');
        
        if (!$terrainId) {
            return response()->json([
                'message' => 'Terrain ID is required',
            ], 422);
        }

        $reviews = Review::where('terrain_id', $terrainId);
        
        $stats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => round($reviews->avg('rating'), 2),
            'rating_distribution' => [
                '5' => $reviews->where('rating', 5)->count(),
                '4' => $reviews->where('rating', 4)->count(),
                '3' => $reviews->where('rating', 3)->count(),
                '2' => $reviews->where('rating', 2)->count(),
                '1' => $reviews->where('rating', 1)->count(),
            ],
        ];

        return response()->json([
            'data' => $stats,
        ]);
    }
}