@extends('layouts.app')

@section('title', 'Shipment ' . $shipment->shipment_number)

@section('content')
<div class="space-y-8">
    <!-- Top Bar: Back & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.index') }}" class="p-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Consignment File</span>
                    <span class="text-xs px-2 py-0.5 rounded bg-brand-500/10 text-brand-400 font-mono border border-brand-500/20">Verified</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-mono mt-0.5">{{ $shipment->shipment_number }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Shareable Public Tracking Link -->
            <a href="{{ route('public.track.show', $shipment->shipment_number) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square text-brand-400"></i> Public URL
            </a>

            @can('update', $shipment)
            <a href="{{ route('shipments.edit', $shipment->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square"></i> Edit Details
            </a>
            @endcan

            @can('delete', $shipment)
            <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" onsubmit="return confirm('Permanently delete shipment?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold transition">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Main Overview Card -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-6">
        <!-- Route Banner & Status Badges -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-800">
            <!-- Origin -> Destination -->
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Origin -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Origin Hub</span>
                    <div class="text-lg sm:text-xl font-bold text-white">{{ $shipment->origin->city }}</div>
                    <p class="text-xs text-slate-400">{{ $shipment->origin->name }} ({{ $shipment->origin->code }})</p>
                    <span class="text-[10px] text-slate-500 font-mono">{{ $shipment->origin->continent }}</span>
                </div>

                <div class="flex flex-col items-center px-2">
                    <span class="text-[10px] text-slate-500 font-mono mb-1">{{ $shipment->carrier->service_type }}</span>
                    <div class="w-20 sm:w-28 flex items-center">
                        <div class="h-0.5 bg-slate-700 flex-1"></div>
                        <i class="fa-solid fa-ship text-brand-400 text-sm mx-2"></i>
                        <div class="h-0.5 bg-slate-700 flex-1"></div>
                    </div>
                    <span class="text-[10px] text-slate-400 font-semibold mt-1">{{ $shipment->carrier->name }}</span>
                </div>

                <!-- Destination -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400">Destination Hub</span>
                    <div class="text-lg sm:text-xl font-bold text-white">{{ $shipment->destination->city }}</div>
                    <p class="text-xs text-slate-400">{{ $shipment->destination->name }} ({{ $shipment->destination->code }})</p>
                    <span class="text-[10px] text-slate-500 font-mono">{{ $shipment->destination->continent }}</span>
                </div>
            </div>

            <!-- Status & Delay Badge -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Status Badge -->
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider
                    {{ $shipment->current_status === 'DELIVERED' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                    {{ $shipment->current_status === 'IN_TRANSIT' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30 animate-pulse' : '' }}
                    {{ in_array($shipment->current_status, ['CREATED', 'BOOKED', 'PICKED_UP']) ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : '' }}
                    {{ $shipment->current_status === 'CANCELLED' ? 'bg-slate-700 text-slate-400' : '' }}">
                    <i class="fa-solid fa-circle text-[8px] mr-1.5"></i>{{ str_replace('_', ' ', $shipment->current_status) }}
                </span>

                <!-- Delay Indicator -->
                @if($shipment->delay_status === 'DELAYED')
                <div class="px-3 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30 flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Delayed (+{{ $shipment->delay_hours }}h - {{ $shipment->delay_severity }})</span>
                </div>
                @else
                <div class="px-3 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <i class="fa-solid fa-clock-check mr-1"></i> On Schedule
                </div>
                @endif
            </div>
        </div>

        <!-- Schedule & Cargo Attributes Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                <span class="text-slate-500 font-semibold uppercase text-[10px] block mb-1">Expected Departure</span>
                <span class="font-bold text-white font-mono">{{ $shipment->expected_departure->format('M d, Y H:i') }}</span>
                @if($shipment->actual_departure)
                <div class="text-[10px] text-emerald-400 mt-1">Departed: {{ $shipment->actual_departure->format('M d, H:i') }}</div>
                @endif
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                <span class="text-slate-500 font-semibold uppercase text-[10px] block mb-1">Expected vs Estimated Arrival</span>
                <span class="font-bold text-white font-mono">{{ $shipment->estimated_arrival->format('M d, Y H:i') }}</span>
                <div class="text-[10px] {{ $shipment->delay_status === 'DELAYED' ? 'text-red-400 font-semibold' : 'text-slate-500' }} mt-1">
                    Sched: {{ $shipment->expected_arrival->format('M d, H:i') }}
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                <span class="text-slate-500 font-semibold uppercase text-[10px] block mb-1">Cargo Type & Volume</span>
                <span class="font-bold text-white line-clamp-1">{{ $shipment->cargo_type }}</span>
                <div class="text-[10px] text-slate-500 mt-1">{{ number_format($shipment->weight_kg, 1) }} kg • {{ number_format($shipment->volume_cbm, 1) }} CBM</div>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                <span class="text-slate-500 font-semibold uppercase text-[10px] block mb-1">Client / Consignee</span>
                <span class="font-bold text-white">{{ $shipment->customer->name }}</span>
                <div class="text-[10px] text-slate-500 mt-1">{{ $shipment->customer->company_name ?? $shipment->customer->email }}</div>
            </div>
        </div>

        @if($shipment->delay_reason)
        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-xs text-amber-300 flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-400 mt-0.5"></i>
            <div>
                <strong class="font-bold">Delay Advisory:</strong> {{ $shipment->delay_reason }}
            </div>
        </div>
        @endif
    </div>

    <!-- Multi-Currency Financial Ledger Card -->
    <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-wave text-emerald-400"></i>
                    <span>Financial Ledger & Multi-Currency Billing</span>
                </h3>
                <p class="text-xs text-slate-400">
                    Base billing currency: <strong class="text-white font-mono">{{ $shipment->currency_code }}</strong>. Display converted dynamically to active preference: <strong class="text-brand-400 font-mono">{{ $activeCurrencyCode }}</strong>.
                </p>
            </div>
            <!-- Quick switcher pill -->
            <span class="text-[11px] font-mono px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-slate-400">
                1 USD = {{ $currencyService->getCurrency($activeCurrencyCode)?->exchange_rate_to_usd ?? 1 }} {{ $activeCurrencyCode }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Freight Cost -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
                <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Ocean / Air Freight Rate</span>
                <div class="text-lg font-bold text-white font-mono">
                    {{ $currencyService->convertAndFormat($shipment->shipping_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </div>
                <div class="text-[10px] text-slate-500 font-mono mt-1">
                    Base: {{ $currencyService->format($shipment->shipping_cost, $shipment->currency_code) }}
                </div>
            </div>

            <!-- Declared Cargo Value -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
                <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Declared Cargo Value</span>
                <div class="text-lg font-bold text-white font-mono">
                    {{ $currencyService->convertAndFormat($shipment->declared_value, $shipment->currency_code, $activeCurrencyCode) }}
                </div>
                <div class="text-[10px] text-slate-500 font-mono mt-1">
                    Base: {{ $currencyService->format($shipment->declared_value, $shipment->currency_code) }}
                </div>
            </div>

            <!-- Marine Insurance -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
                <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Cargo Insurance Premium</span>
                <div class="text-lg font-bold text-white font-mono">
                    {{ $currencyService->convertAndFormat($shipment->insurance_fee, $shipment->currency_code, $activeCurrencyCode) }}
                </div>
                <div class="text-[10px] text-slate-500 font-mono mt-1">
                    Base: {{ $currencyService->format($shipment->insurance_fee, $shipment->currency_code) }}
                </div>
            </div>

            <!-- Total Logistics Charge -->
            <div class="p-4 rounded-2xl bg-brand-950/40 border border-brand-500/30">
                <span class="text-[10px] uppercase font-bold text-brand-300 block mb-1">Total Transaction Payable</span>
                <div class="text-lg font-extrabold text-brand-400 font-mono">
                    {{ $currencyService->convertAndFormat($shipment->total_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-1">
                    Base: {{ $currencyService->format($shipment->total_cost, $shipment->currency_code) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Leaflet.js World Map -->
    <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-earth-americas text-brand-400"></i>
                    <span>Interactive Global Voyage Map</span>
                </h3>
                <p class="text-xs text-slate-400">
                    Current Location: <strong class="text-slate-200">{{ $shipment->current_location_name ?: 'En Route' }}</strong>
                    @if($shipment->current_latitude)
                    ({{ sprintf('%.4f, %.4f', $shipment->current_latitude, $shipment->current_longitude) }})
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-emerald-400"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Origin</span>
                <span class="flex items-center gap-1.5 text-amber-400"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span> Vessel</span>
                <span class="flex items-center gap-1.5 text-blue-400"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Destination</span>
            </div>
        </div>

        <div id="shipmentDetailMap" class="w-full h-[450px] rounded-2xl border border-slate-800 overflow-hidden shadow-inner z-0"></div>
    </div>

    <!-- Operator / Admin Control Panel (Status Transitions & Tracking) -->
    @can('update', $shipment)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Lifecycle Status Transition -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-arrows-spin text-brand-400"></i>
                <span>Advance Lifecycle Status</span>
            </h3>
            <p class="text-xs text-slate-400">
                Transitions are governed by strict business rules. Current state: <strong class="text-slate-200">{{ $shipment->current_status }}</strong>
            </p>

            @if($shipment->isTerminal())
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-lock text-slate-500"></i>
                <span>This consignment is in a terminal state ({{ $shipment->current_status }}) and cannot be transitioned further.</span>
            </div>
            @else
            <form action="{{ route('shipments.status', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Target Milestone</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        @foreach(\App\Models\Shipment::VALID_TRANSITIONS[$shipment->current_status] ?? [] as $next)
                            <option value="{{ $next }}">{{ $next }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Transition Log Notes</label>
                    <input type="text" name="notes" placeholder="e.g. Master signed departure manifest." class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-lg shadow-brand-500/20 transition">
                    Execute Status Transition
                </button>
            </form>
            @endif
        </div>

        <!-- 2. Quick Log Tracking Checkpoint -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-location-crosshairs text-cyan-400"></i>
                <span>Log Verified Checkpoint</span>
            </h3>
            <p class="text-xs text-slate-400">Appends verifiable geolocation coordinate with external weather integration.</p>

            <form action="{{ route('shipments.tracking.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Checkpoint Status</label>
                        <select name="status" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                            <option value="IN_TRANSIT">IN TRANSIT</option>
                            <option value="PORT_ARRIVAL">PORT ARRIVAL</option>
                            <option value="CUSTOMS_CLEARANCE">CUSTOMS CLEARANCE</option>
                            <option value="DEPARTED">DEPARTED</option>
                            <option value="DELIVERED">DELIVERED</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Location / Port Name</label>
                        <input type="text" name="location_name" required placeholder="e.g. Malacca Strait Anchorage" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" placeholder="e.g. 2.1896" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" placeholder="e.g. 102.2501" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Log Description</label>
                    <input type="text" name="description" required placeholder="Checkpoint verified by telemetry/operator." class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs border border-slate-700 transition">
                    Append Verified Checkpoint
                </button>
            </form>
        </div>
    </div>
    @endcan

    <!-- Exception Management & SLA Incident Center -->
    <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                    <span>Incident & Exception Management</span>
                </h3>
                <p class="text-xs text-slate-400">Enforces deterministic delay calculation and operational resolution SLA</p>
            </div>

            @can('update', $shipment)
            <!-- Button to toggle Exception Modal or Form -->
            <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.toggle('hidden')" class="px-3.5 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Raise Incident Exception
            </button>
            @endcan
        </div>

        <!-- Hidden Drawer to Raise Exception -->
        <div id="raiseExceptionDrawer" class="hidden p-5 rounded-2xl bg-slate-950 border border-red-500/30 space-y-4">
            <h4 class="text-xs font-bold uppercase text-red-400 tracking-wider">Log New Operational Exception</h4>
            <form action="{{ route('shipments.exceptions.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Exception Classification</label>
                        <select name="exception_type" required class="w-full px-3 py-2 text-xs bg-slate-900 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-red-500">
                            <option value="CUSTOMS_HOLD">CUSTOMS HOLD</option>
                            <option value="WEATHER_DELAY">WEATHER DELAY</option>
                            <option value="PORT_CONGESTION">PORT CONGESTION</option>
                            <option value="DAMAGED">DAMAGED CARGO</option>
                            <option value="MISROUTED">MISROUTED</option>
                            <option value="DOCUMENTATION_ISSUE">DOCUMENTATION ISSUE</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Severity Level</label>
                        <select name="severity" required class="w-full px-3 py-2 text-xs bg-slate-900 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-red-500">
                            <option value="LOW">LOW (0 - 6 hrs)</option>
                            <option value="MEDIUM" selected>MEDIUM (6 - 24 hrs)</option>
                            <option value="HIGH">HIGH (> 24 hrs)</option>
                            <option value="CRITICAL">CRITICAL (Impasse)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Delay Impact (Hours)</label>
                        <input type="number" name="delay_impact_hours" min="0" value="12" class="w-full px-3 py-2 text-xs bg-slate-900 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Root Cause Description</label>
                    <textarea name="root_cause" required rows="2" placeholder="Explain the root cause of the hold, delay, or damage..." class="w-full px-3 py-2 text-xs bg-slate-900 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-red-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Corrective Action Plan</label>
                    <input type="text" name="action_plan" placeholder="Steps taken to clear the exception..." class="w-full px-3 py-2 text-xs bg-slate-900 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-red-500">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.add('hidden')" class="px-3 py-1.5 rounded-lg text-xs text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white font-semibold text-xs transition">Submit Incident</button>
                </div>
            </form>
        </div>

        <!-- Exceptions List -->
        <div class="space-y-3">
            @forelse($shipment->exceptions as $exc)
            <div class="p-4 rounded-2xl {{ $exc->isOpen() ? 'bg-red-500/10 border border-red-500/30' : 'bg-slate-950/60 border border-slate-800' }} flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase {{ $exc->isOpen() ? 'text-red-400' : 'text-emerald-400' }}">
                            {{ $exc->exception_type }}
                        </span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase
                            {{ $exc->severity === 'CRITICAL' ? 'bg-red-500 text-white' : '' }}
                            {{ $exc->severity === 'HIGH' ? 'bg-orange-500/20 text-orange-400' : '' }}
                            {{ $exc->severity === 'MEDIUM' ? 'bg-amber-500/20 text-amber-400' : '' }}
                            {{ $exc->severity === 'LOW' ? 'bg-blue-500/20 text-blue-400' : '' }}">
                            {{ $exc->severity }}
                        </span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase {{ $exc->isOpen() ? 'bg-red-950 text-red-300' : 'bg-emerald-950 text-emerald-300' }}">
                            {{ $exc->status }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-200"><strong>Root Cause:</strong> {{ $exc->root_cause }}</p>
                    @if($exc->action_plan)
                    <p class="text-[11px] text-slate-400"><strong>Action Plan:</strong> {{ $exc->action_plan }}</p>
                    @endif
                    @if($exc->resolution_notes)
                    <p class="text-[11px] text-emerald-400"><strong>Resolution:</strong> {{ $exc->resolution_notes }} (Resolved at {{ $exc->resolved_at?->format('M d, H:i') }})</p>
                    @endif
                    <div class="text-[10px] text-slate-500">
                        Detected: {{ $exc->detected_at->format('M d, Y H:i') }} • Logged by {{ $exc->reportedBy?->name ?? 'System' }}
                    </div>
                </div>

                @if($exc->isOpen() && Auth::user()->canManageShipments())
                <!-- Resolve Form -->
                <form action="{{ route('exceptions.resolve', $exc->id) }}" method="POST" class="flex items-center gap-2 shrink-0">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="resolution_notes" required placeholder="Resolution summary..." class="px-3 py-1.5 text-xs bg-slate-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none">
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow transition">
                        Resolve
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="p-4 text-center rounded-2xl bg-slate-950/40 text-xs text-slate-500">
                <i class="fa-solid fa-shield-check text-emerald-400 mr-1.5"></i> No active exceptions or incidents logged for this voyage.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Chronological Tracking Timeline & Items Manifest Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Timeline (2 Columns) -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl lg:col-span-2 space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-timeline text-brand-400"></i>
                <span>Chronological Checkpoint Timeline</span>
            </h3>

            <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                @forelse($shipment->trackingEvents as $ev)
                <div class="relative group">
                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px]
                        {{ $ev->is_exception ? 'bg-red-500 text-white ring-4 ring-red-500/20' : 'bg-slate-800 border-2 border-brand-500 text-brand-400' }}">
                        @if($ev->is_exception)
                            <i class="fa-solid fa-exclamation"></i>
                        @else
                            <i class="fa-solid fa-check"></i>
                        @endif
                    </div>

                    <div class="p-4 rounded-2xl {{ $ev->is_exception ? 'bg-red-500/10 border border-red-500/25' : 'bg-slate-950/60 border border-slate-800/80' }}">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white">{{ $ev->status }}</span>
                                <span class="text-xs text-slate-400">• {{ $ev->location_name }}</span>
                            </div>
                            <span class="text-[11px] font-mono text-slate-500">{{ $ev->event_time->format('M d, Y H:i') }} UTC</span>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">{{ $ev->description }}</p>
                        @if($ev->weather_summary)
                        <div class="mt-2 inline-flex items-center gap-1.5 text-[11px] text-cyan-400 bg-cyan-950/40 px-2 py-0.5 rounded border border-cyan-800/50">
                            <i class="fa-solid fa-cloud-sun"></i> {{ $ev->weather_summary }}
                        </div>
                        @endif
                        @if($ev->createdByUser)
                        <span class="block text-[10px] text-slate-500 mt-2">Operator: {{ $ev->createdByUser->name }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-500">No checkpoints logged yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Manifest Cargo Items (1 Column) -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-dolly text-brand-400"></i>
                <span>Cargo Manifest</span>
            </h3>

            <div class="space-y-3">
                @forelse($shipment->items as $item)
                <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="flex items-start justify-between gap-2">
                        <h4 class="text-xs font-bold text-slate-200 line-clamp-1">{{ $item->item_description }}</h4>
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-800 text-slate-300">{{ $item->package_type }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2 text-[11px] text-slate-400">
                        <div>Qty: <strong class="text-white">{{ $item->quantity }}</strong></div>
                        <div>Weight: <strong class="text-white">{{ number_format($item->weight_kg, 1) }} kg</strong></div>
                        <div>HS Code: <span class="font-mono text-slate-300">{{ $item->hs_code ?: 'N/A' }}</span></div>
                        <div>Value: <span class="font-mono text-emerald-400">{{ $currencyService->format($item->declared_value, $shipment->currency_code) }}</span></div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-500">Single consolidated container consignment.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mapData = {!! json_encode($mapPoints) !!};

        if (!mapData || mapData.length === 0) return;

        // Default center on latest coordinate
        const centerPoint = mapData[mapData.length - 1] || mapData[0];
        const map = L.map('shipmentDetailMap', {
            center: [centerPoint.lat, centerPoint.lng],
            zoom: 3,
            minZoom: 2,
            maxZoom: 16,
            worldCopyJump: true
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        const latLngs = [];

        mapData.forEach(function (pt) {
            latLngs.push([pt.lat, pt.lng]);

            let color = '#3b82f6';
            let radius = 6;

            if (pt.type === 'origin') {
                color = '#10b981';
                radius = 8;
            } else if (pt.type === 'destination') {
                color = '#3b82f6';
                radius = 8;
            } else if (pt.type === 'exception') {
                color = '#ef4444';
                radius = 7;
            } else if (pt.type === 'current') {
                color = '#f59e0b';
                radius = 9;
            }

            const marker = L.circleMarker([pt.lat, pt.lng], {
                radius: radius,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            let popupContent = `<b>${pt.title}</b><br>${pt.location}`;
            if (pt.time) popupContent += `<br><span style="color:#94a3b8;font-size:11px;">Time: ${pt.time}</span>`;
            if (pt.weather) popupContent += `<br><span style="color:#38bdf8;font-size:11px;">Weather: ${pt.weather}</span>`;
            marker.bindPopup(popupContent);

            if (pt.type === 'current') {
                marker.openPopup();
            }
        });

        // Draw Great Route line
        if (latLngs.length > 1) {
            const polyline = L.polyline(latLngs, {
                color: '#2563eb',
                weight: 3.5,
                opacity: 0.8,
                dashArray: '5, 8'
            }).addTo(map);

            map.fitBounds(polyline.getBounds(), { padding: [40, 40] });
        }
    });
</script>
@endpush
