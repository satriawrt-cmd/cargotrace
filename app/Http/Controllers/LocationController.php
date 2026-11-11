<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Administrator access required.');
        }

        $locations = Location::withCount(['originShipments', 'destinationShipments'])
            ->orderBy('continent')
            ->orderBy('country')
            ->get();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Administrator access required.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:locations,code'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'continent' => ['required', 'string', 'in:Asia,Europe,North America,South America,Africa,Oceania'],
            'type' => ['required', 'string', 'in:SEAPORT,AIRPORT,LOGISTICS_HUB'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ]);

        $location = Location::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_LOCATION',
            'entity_type' => 'Location',
            'entity_id' => $location->id,
            'new_values' => $location->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Location {$location->name} added successfully.");
    }
}
