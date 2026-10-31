<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarrierApiController extends Controller
{
    public function index(): JsonResponse
    {
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        return response()->json(['data' => $carriers], 200);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->canManageShipments()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'service_type' => ['required', 'string', 'in:SEA_FREIGHT,AIR_FREIGHT,MULTIMODAL'],
            'tracking_url_template' => ['nullable', 'url'],
        ]);

        $carrier = Carrier::create($validated);

        return response()->json([
            'message' => 'Carrier created successfully.',
            'data' => $carrier,
        ], 201);
    }
}
