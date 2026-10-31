<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    public function index(): JsonResponse
    {
        $locations = Location::orderBy('name')->get();
        return response()->json(['data' => $locations], 200);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->canManageShipments()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:locations,code'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'continent' => ['required', 'string'],
            'type' => ['required', 'string', 'in:SEAPORT,AIRPORT,LOGISTICS_HUB'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string'],
        ]);

        $location = Location::create($validated);

        return response()->json([
            'message' => 'Location created successfully.',
            'data' => $location,
        ], 201);
    }
}
