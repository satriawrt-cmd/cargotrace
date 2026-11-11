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

        // Base query respecting user role
        $baseQuery = Shipment::query()->forUser($user);

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
            'activeExceptions'
        ));
    }
}
