<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrierController extends Controller
{
    public function index()
    {
        $carriers = Carrier::withCount('shipments')->orderBy('name')->get();
        return view('carriers.index', compact('carriers'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canManageShipments()) {
            abort(403);
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

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_CARRIER',
            'entity_type' => 'Carrier',
            'entity_id' => $carrier->id,
            'new_values' => $carrier->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Carrier {$carrier->name} added successfully.");
    }

    public function update(Request $request, Carrier $carrier)
    {
        if (!Auth::user()->canManageShipments()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'service_type' => ['required', 'string', 'in:SEA_FREIGHT,AIR_FREIGHT,MULTIMODAL'],
            'is_active' => ['boolean'],
        ]);

        $carrier->update($validated);

        return back()->with('success', "Carrier {$carrier->name} updated.");
    }
}
