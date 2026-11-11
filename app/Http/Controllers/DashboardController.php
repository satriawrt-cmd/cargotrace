<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Shipment;
use App\Models\ShipmentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query respecting user role (if not logged in, show all public consignments)
        $baseQuery = Shipment::query();
        if ($user) {
            $baseQuery->forUser($user);
        }

        $totalShipments = (clone $baseQuery)->count();
        $inTransitCount = (clone $baseQuery)->where('current_status', Shipment::STATUS_IN_TRANSIT)->count();
        $deliveredCount = (clone $baseQuery)->where('current_status', Shipment::STATUS_DELIVERED)->count();
        $delayedCount = (clone $baseQuery)->where('delay_status', 'DELAYED')->count();

        // Open exceptions
        $openExceptionsQuery = ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING']);
        if ($user && $user->isCustomer()) {
            $openExceptionsQuery->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }
        $openExceptionsCount = $openExceptionsQuery->count();

        // Calculate KPI rates
        $onTimeRate = $totalShipments > 0
            ? round((($totalShipments - $delayedCount) / $totalShipments) * 100, 1)
            : 100.0;

        $avgDelayHours = $delayedCount > 0
            ? round((clone $baseQuery)->where('delay_status', 'DELAYED')->avg('delay_hours') ?? 0, 1)
            : 0.0;

        // Status breakdown for Chart.js
        $statuses = [
            Shipment::STATUS_CREATED,
            Shipment::STATUS_BOOKED,
            Shipment::STATUS_PICKED_UP,
            Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_DELIVERED,
        ];
        $statusCounts = [];
        foreach ($statuses as $st) {
            $statusCounts[$st] = (clone $baseQuery)->where('current_status', $st)->count();
        }

        // Carrier breakdown for Chart.js
        $carrierCounts = [];
        $carriers = Carrier::where('is_active', true)->get();
        foreach ($carriers as $c) {
            $count = (clone $baseQuery)->where('carrier_id', $c->id)->count();
            if ($count > 0 || !$user || !$user->isCustomer()) {
                $carrierCounts[$c->name] = $count;
            }
        }

        // Delay severity breakdown
        $delaySeverityCounts = [
            'LOW' => (clone $baseQuery)->where('delay_severity', 'LOW')->count(),
            'MEDIUM' => (clone $baseQuery)->where('delay_severity', 'MEDIUM')->count(),
            'HIGH' => (clone $baseQuery)->where('delay_severity', 'HIGH')->count(),
        ];

        // Recent shipments with relations
        $recentShipments = (clone $baseQuery)
            ->with(['carrier', 'origin', 'destination', 'customer'])
            ->latest()
            ->take(8)
            ->get();

        // Active critical exceptions
        $activeExceptions = $openExceptionsQuery
            ->with(['shipment.carrier', 'shipment.origin', 'shipment.destination'])
            ->latest('detected_at')
            ->take(5)
            ->get();

        // -------------------------------------------------------------------
        // Integrated Live Tracker directly within Dashboard
        // -------------------------------------------------------------------
        $trackedNumber = trim($request->input('track', $request->input('shipment_number', '')));
        $trackedShipment = null;

        if ($trackedNumber) {
            $findQuery = Shipment::query();
            if ($user && $user->isCustomer()) {
                $findQuery->where('customer_id', $user->id);
            }
            $trackedShipment = $findQuery->where('shipment_number', $trackedNumber)
                ->with(['carrier', 'origin', 'destination', 'customer', 'trackingEvents', 'exceptions'])
                ->first();
        }

        // Default to a representative active consignment if none specified
        if (!$trackedShipment) {
            $defaultQuery = Shipment::query();
            if ($user && $user->isCustomer()) {
                $defaultQuery->where('customer_id', $user->id);
            }

            // Prefer IN_TRANSIT shipment, otherwise latest
            $trackedShipment = (clone $defaultQuery)->where('current_status', Shipment::STATUS_IN_TRANSIT)
                ->with(['carrier', 'origin', 'destination', 'customer', 'trackingEvents', 'exceptions'])
                ->latest()
                ->first();

            if (!$trackedShipment) {
                $trackedShipment = (clone $defaultQuery)
                    ->with(['carrier', 'origin', 'destination', 'customer', 'trackingEvents', 'exceptions'])
                    ->latest()
                    ->first();
            }
        }

        // Prepare map coordinates sequence for Leaflet inside Dashboard
        $mapPoints = [];
        if ($trackedShipment) {
            if ($trackedShipment->origin) {
                $mapPoints[] = [
                    'type' => 'origin',
                    'title' => 'Pelabuhan Asal: ' . $trackedShipment->origin->name,
                    'location' => $trackedShipment->origin->city . ', ' . $trackedShipment->origin->country,
                    'time' => $trackedShipment->expected_departure?->format('M d, Y H:i'),
                    'lat' => (float) $trackedShipment->origin->latitude,
                    'lng' => (float) $trackedShipment->origin->longitude,
                ];
            }

            foreach ($trackedShipment->trackingEvents as $ev) {
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

            if ($trackedShipment->current_latitude && $trackedShipment->current_longitude) {
                $mapPoints[] = [
                    'type' => 'current',
                    'title' => 'Posisi Kargo: ' . ($trackedShipment->current_location_name ?: 'En Route'),
                    'location' => $trackedShipment->current_location_name,
                    'lat' => (float) $trackedShipment->current_latitude,
                    'lng' => (float) $trackedShipment->current_longitude,
                ];
            }

            if ($trackedShipment->destination) {
                $mapPoints[] = [
                    'type' => 'destination',
                    'title' => 'Pelabuhan Tujuan: ' . $trackedShipment->destination->name,
                    'location' => $trackedShipment->destination->city . ', ' . $trackedShipment->destination->country,
                    'lat' => (float) $trackedShipment->destination->latitude,
                    'lng' => (float) $trackedShipment->destination->longitude,
                ];
            }
        }

        // Demo chips for quick tracking
        $demoShipmentsQuery = Shipment::query();
        if ($user && $user->isCustomer()) {
            $demoShipmentsQuery->where('customer_id', $user->id);
        }
        $quickDemoShipments = $demoShipmentsQuery->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalShipments',
            'inTransitCount',
            'deliveredCount',
            'delayedCount',
            'openExceptionsCount',
            'onTimeRate',
            'avgDelayHours',
            'statusCounts',
            'carrierCounts',
            'delaySeverityCounts',
            'recentShipments',
            'activeExceptions',
            'trackedShipment',
            'mapPoints',
            'trackedNumber',
            'quickDemoShipments'
        ));
    }
}
