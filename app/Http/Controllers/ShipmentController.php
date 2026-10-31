<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Carrier;
use App\Models\Currency;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
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
            ->with(['customer', 'carrier', 'origin', 'destination'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('shipments.index', compact('shipments', 'carriers', 'locations', 'filters'));
    }

    public function show(Shipment $shipment)
    {
        Gate::authorize('view', $shipment);

        $shipment->load([
            'customer',
            'carrier',
            'origin',
            'destination',
            'items',
            'trackingEvents.createdByUser',
            'exceptions.reportedBy',
            'exceptions.resolvedBy',
        ]);

        // Prepare map coordinates sequence for Leaflet.js
        $mapPoints = [];

        // Origin
        if ($shipment->origin) {
            $mapPoints[] = [
                'type' => 'origin',
                'title' => 'Origin: ' . $shipment->origin->name,
                'location' => $shipment->origin->city . ', ' . $shipment->origin->country,
                'lat' => (float) $shipment->origin->latitude,
                'lng' => (float) $shipment->origin->longitude,
            ];
        }

        // Intermediate checkpoints
        foreach ($shipment->trackingEvents as $ev) {
            if ($ev->latitude && $ev->longitude) {
                $mapPoints[] = [
                    'type' => $ev->is_exception ? 'exception' : 'checkpoint',
                    'title' => $ev->status . ' - ' . $ev->location_name,
                    'location' => $ev->location_name,
                    'time' => $ev->event_time->format('M d, Y H:i'),
                    'description' => $ev->description,
                    'weather' => $ev->weather_summary,
                    'lat' => (float) $ev->latitude,
                    'lng' => (float) $ev->longitude,
                ];
            }
        }

        // Current Position (if different from destination)
        if ($shipment->current_latitude && $shipment->current_longitude) {
            $mapPoints[] = [
                'type' => 'current',
                'title' => 'Current Position: ' . ($shipment->current_location_name ?: 'En Route'),
                'location' => $shipment->current_location_name,
                'lat' => (float) $shipment->current_latitude,
                'lng' => (float) $shipment->current_longitude,
            ];
        }

        // Destination
        if ($shipment->destination) {
            $mapPoints[] = [
                'type' => 'destination',
                'title' => 'Destination: ' . $shipment->destination->name,
                'location' => $shipment->destination->city . ', ' . $shipment->destination->country,
                'lat' => (float) $shipment->destination->latitude,
                'lng' => (float) $shipment->destination->longitude,
            ];
        }

        return view('shipments.show', compact('shipment', 'mapPoints'));
    }

    public function create()
    {
        Gate::authorize('create', Shipment::class);

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();

        return view('shipments.create', compact('carriers', 'locations', 'currencies', 'customers'));
    }

    public function store(StoreShipmentRequest $request)
    {
        $shipment = $this->shipmentService->createShipment(
            $request->validated(),
            Auth::user()
        );

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', "Shipment {$shipment->shipment_number} created successfully!");
    }

    public function edit(Shipment $shipment)
    {
        Gate::authorize('update', $shipment);

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('shipments.edit', compact('shipment', 'carriers', 'locations', 'currencies'));
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        Gate::authorize('update', $shipment);

        $this->shipmentService->updateShipment(
            $shipment,
            $request->validated(),
            Auth::user()
        );

        return redirect()->route('shipments.show', $shipment->id)
            ->with('success', "Shipment {$shipment->shipment_number} updated successfully!");
    }

    public function destroy(Shipment $shipment)
    {
        Gate::authorize('delete', $shipment);

        $number = $shipment->shipment_number;
        $shipment->delete();

        return redirect()->route('shipments.index')
            ->with('success', "Shipment {$number} deleted successfully.");
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        Gate::authorize('update', $shipment);

        $validated = $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->shipmentService->transitionStatus(
                $shipment,
                $validated['status'],
                $validated['notes'] ?? null,
                Auth::user()
            );

            return back()->with('success', "Status transitioned to {$validated['status']}.");
        } catch (\Throwable $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }
}
