<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrackingEventRequest;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TrackingController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    public function store(StoreTrackingEventRequest $request, Shipment $shipment)
    {
        Gate::authorize('addTracking', $shipment);

        try {
            $this->trackingService->addEvent(
                $shipment,
                $request->validated(),
                Auth::user()
            );

            return back()->with('success', 'Tracking checkpoint logged successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors(['tracking_error' => $e->getMessage()]);
        }
    }
}
