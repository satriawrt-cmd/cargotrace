<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class PublicTrackingController extends Controller
{
    public function index(Request $request)
    {
        $shipmentNumber = trim($request->input('shipment_number', ''));
        $shipment = null;

        if ($shipmentNumber) {
            $shipment = Shipment::where('shipment_number', $shipmentNumber)
                ->with(['carrier', 'origin', 'destination', 'trackingEvents', 'exceptions'])
                ->first();
        }

        return view('public.track', compact('shipment', 'shipmentNumber'));
    }

    public function show(string $number)
    {
        $shipment = Shipment::where('shipment_number', $number)
            ->with(['carrier', 'origin', 'destination', 'trackingEvents', 'exceptions'])
            ->firstOrFail();

        return view('public.track', [
            'shipment' => $shipment,
            'shipmentNumber' => $number,
        ]);
    }
}
