<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ShipmentApiController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = $request->only([
            'search',
            'status',
            'carrier_id',
            'origin_id',
            'destination_id',
            'delay_status',
            'date_from',
            'date_to',
        ]);

        $shipments = Shipment::query()
            ->forUser($user)
            ->filter($filters)
            ->with(['carrier', 'origin', 'destination', 'customer:id,name,email,company_name'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json($shipments, 200);
    }

    public function store(StoreShipmentRequest $request): JsonResponse
    {
        $shipment = $this->shipmentService->createShipment(
            $request->validated(),
            $request->user()
        );

        $shipment->load(['carrier', 'origin', 'destination', 'items']);

        return response()->json([
            'message' => 'Shipment created successfully.',
            'data' => $shipment,
        ], 201);
    }

    public function show(Request $request, Shipment $shipment): JsonResponse
    {
        Gate::authorize('view', $shipment);

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.createdByUser:id,name,email',
            'exceptions.reportedBy:id,name',
            'exceptions.resolvedBy:id,name',
        ]);

        return response()->json(['data' => $shipment], 200);
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment): JsonResponse
    {
        Gate::authorize('update', $shipment);

        $updated = $this->shipmentService->updateShipment(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Shipment updated successfully.',
            'data' => $updated,
        ], 200);
    }

    public function destroy(Shipment $shipment): JsonResponse
    {
        Gate::authorize('delete', $shipment);

        $shipment->delete();

        return response()->json([
            'message' => 'Shipment deleted successfully.',
        ], 200);
    }
}
