<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrackingEventRequest;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TrackingApiController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function index(Request $request, Shipment $shipment): JsonResponse
    {
        Gate::authorize('view', $shipment);

        $events = $shipment->trackingEvents()
            ->with('createdByUser:id,name')
            ->orderBy('event_time', 'asc')
            ->get();

        return response()->json([
            'shipment_number' => $shipment->shipment_number,
            'current_status' => $shipment->current_status,
            'current_location' => $shipment->current_location_name,
            'current_coordinates' => [
                'latitude' => $shipment->current_latitude,
                'longitude' => $shipment->current_longitude,
            ],
            'events' => $events,
        ], 200);
    }

    public function store(StoreTrackingEventRequest $request, Shipment $shipment): JsonResponse
    {
        Gate::authorize('addTracking', $shipment);

        $event = $this->trackingService->addEvent(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Tracking event logged.',
            'event' => $event,
            'shipment' => $shipment->fresh(),
        ], 201);
    }
}
