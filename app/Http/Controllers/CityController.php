<?php

namespace App\Http\Controllers;

use App\Http\Requests\City\StoreCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * List cities with pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $cities = City::query()
            ->when($request->string('q'), function ($q, $term) {
                $q->where('name', 'like', "%{$term}%");
            })
            ->orderBy('name')
            ->paginate((int) $request->integer('per_page', 15));

        return response()->json($cities);
    }

    /**
     * Create a new city.
     */
    public function store(StoreCityRequest $request): JsonResponse
    {
        $city = City::create($request->validated());
        return response()->json($city, 201);
    }

    /**
     * Show a city.
     */
    public function show(City $city): JsonResponse
    {
        return response()->json($city);
    }

    /**
     * Delete a city.
     */
    public function destroy(City $city): JsonResponse
    {
        $city->delete();
        return response()->json(null, 204);
    }

    /**
     * Mark as favorite for current user.
     */
    public function favorite(Request $request, City $city): JsonResponse
    {
        $request->user()->cities()->syncWithoutDetaching([$city->id => ['is_primary' => false]]);
        return response()->json(['message' => 'Favorited']);
    }

    /**
     * Unfavorite for current user.
     */
    public function unfavorite(Request $request, City $city): JsonResponse
    {
        $request->user()->cities()->detach($city->id);
        return response()->json(['message' => 'Unfavorited']);
    }
}

