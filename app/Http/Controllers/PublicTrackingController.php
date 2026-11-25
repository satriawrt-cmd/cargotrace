<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicTrackingController extends Controller
{
    /**
     * Dedicated Live Tracker Page (Integrated within the same app layout).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $shipmentNumber = trim($request->input('shipment_number', $request->input('track', '')));
        $shipment = null;

        if ($shipmentNumber) {
            $query = Shipment::query();
            if ($user && $user->isCustomer()) {
                $query->where('customer_id', $user->id);
            }
            $shipment = $query->where('shipment_number', $shipmentNumber)
                ->with(['carrier', 'origin', 'destination', 'customer', 'trackingEvents', 'exceptions'])
                ->first();
        }

        // If no consignment specified, load a smart active default so map displays immediately
        if (!$shipment) {
            $defaultQuery = Shipment::query();
            if ($user && $user->isCustomer()) {
                $defaultQuery->where('customer_id', $user->id);
            }

            // Prefer IN_TRANSIT shipment, then latest
            $shipment = (clone $defaultQuery)->where('current_status', Shipment::STATUS_IN_TRANSIT)
                ->with(['carrier', 'origin', 'destination', 'customer', 'trackingEvents', 'exceptions'])
                ->latest()
                ->first();

            if (!$shipment) {
                $shipment = (clone $defaultQuery)
                    ->with(['carrier', 'origin', 'destination', 'customer', 'trackingEvents', 'exceptions'])
                    ->latest()
                    ->first();
            }

            if ($shipment && empty($shipmentNumber)) {
                $shipmentNumber = $shipment->shipment_number;
            }
        }

        // Map points sequence for Leaflet.js
        $mapPoints = [];
        $isId = \Illuminate\Support\Facades\App::getLocale() === 'id' || session('locale') === 'id';
        if ($shipment) {
            if ($shipment->origin) {
                $mapPoints[] = [
                    'type' => 'origin',
                    'title' => ($isId ? 'Pelabuhan Asal: ' : 'Origin Port: ') . $shipment->origin->name,
                    'location' => $shipment->origin->city . ', ' . $shipment->origin->country,
                    'time' => $shipment->expected_departure?->format('d M Y H:i'),
                    'lat' => (float) $shipment->origin->latitude,
                    'lng' => (float) $shipment->origin->longitude,
                ];
            }

            foreach ($shipment->trackingEvents as $ev) {
                if ($ev->latitude && $ev->longitude) {
                    $mapPoints[] = [
                        'type' => $ev->is_exception ? 'exception' : 'checkpoint',
                        'title' => $ev->localized_status . ' - ' . $ev->localized_location_name,
                        'location' => $ev->localized_location_name,
                        'time' => $ev->event_time->format('d M Y H:i'),
                        'description' => $ev->localized_description,
                        'weather' => $ev->localized_weather,
                        'lat' => (float) $ev->latitude,
                        'lng' => (float) $ev->longitude,
                    ];
                }
            }

            if ($shipment->current_latitude && $shipment->current_longitude) {
                $mapPoints[] = [
                    'type' => 'current',
                    'title' => ($isId ? 'Posisi Kargo: ' : 'Cargo Position: ') . \App\Services\TranslationHelper::translateText($shipment->current_location_name ?: 'En Route'),
                    'location' => \App\Services\TranslationHelper::translateText($shipment->current_location_name),
                    'lat' => (float) $shipment->current_latitude,
                    'lng' => (float) $shipment->current_longitude,
                ];
            }

            if ($shipment->destination) {
                $mapPoints[] = [
                    'type' => 'destination',
                    'title' => ($isId ? 'Pelabuhan Tujuan: ' : 'Destination Port: ') . $shipment->destination->name,
                    'location' => $shipment->destination->city . ', ' . $shipment->destination->country,
                    'lat' => (float) $shipment->destination->latitude,
                    'lng' => (float) $shipment->destination->longitude,
                ];
            }
        }

        // Demo / quick select chips
        $demoQuery = Shipment::query();
        if ($user && $user->isCustomer()) {
            $demoQuery->where('customer_id', $user->id);
        }
        $quickDemoShipments = $demoQuery->latest()->take(5)->get();

        return view('public.track', compact('shipment', 'shipmentNumber', 'mapPoints', 'quickDemoShipments'));
    }

    public function show(string $number)
    {
        return $this->index(request()->merge(['shipment_number' => $number]));
    }
}
