@extends('layouts.app')

@section('title', 'Shipment ' . $shipment->shipment_number)

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.index') }}" class="p-2 rounded-xl bg-white hover:bg-slate-50 text-slate-500 border border-slate-200 shadow-xs transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Consignment</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 font-mono font-bold">VERIFIED</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-mono mt-0.5">{{ $shipment->shipment_number }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('public.track.show', $shipment->shipment_number) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up-right-from-square text-blue-600 text-[11px]"></i>
                <span>Public URL</span>
            </a>

            @can('update', $shipment)
            <a href="{{ route('shipments.edit', $shipment->id) }}" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square text-slate-500 text-[11px]"></i>
                <span>Edit</span>
            </a>
            @endcan

            @can('delete', $shipment)
            <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" onsubmit="return confirm('Permanently delete shipment?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold transition">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Main Overview Card (Clean Soft White Style) -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-6">
        <!-- Route Banner & Status -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <!-- Origin -> Destination -->
            <div class="flex items-center gap-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Origin</span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ $shipment->origin->city }}</h3>
                    <p class="text-xs text-slate-500">{{ $shipment->origin->country }} ({{ $shipment->origin->code }})</p>
                </div>

                <div class="flex flex-col items-center px-2">
                    <span class="text-[10px] text-slate-400 mb-0.5">{{ $shipment->carrier->service_type }}</span>
                    <div class="w-20 sm:w-28 flex items-center">
                        <div class="h-0.5 bg-slate-200 flex-1"></div>
                        <i class="fa-solid fa-ship text-blue-600 text-xs mx-2"></i>
                        <div class="h-0.5 bg-slate-200 flex-1"></div>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-600 mt-0.5">{{ $shipment->carrier->name }}</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Destination</span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ $shipment->destination->city }}</h3>
                    <p class="text-xs text-slate-500">{{ $shipment->destination->country }} ({{ $shipment->destination->code }})</p>
                </div>
            </div>

            <!-- Status & Delay Badge -->
            <div class="flex items-center gap-2">
                @if($shipment->current_status === 'DELIVERED')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Delivered
                    </span>
                @elseif($shipment->current_status === 'IN_TRANSIT')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        In Transit
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ str_replace('_', ' ', $shipment->current_status) }}
                    </span>
                @endif

                @if($shipment->delay_status === 'DELAYED')
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                        Delayed (+{{ $shipment->delay_hours }}h)
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        On Schedule
                    </span>
                @endif
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block mb-1">Departure Schedule</span>
                <span class="font-bold text-slate-800 font-mono">{{ $shipment->expected_departure->format('M d, Y H:i') }}</span>
                @if($shipment->actual_departure)
                <p class="text-[10px] text-emerald-600 mt-1">Actual: {{ $shipment->actual_departure->format('M d, H:i') }}</p>
                @endif
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block mb-1">Estimated Arrival</span>
                <span class="font-bold text-slate-800 font-mono">{{ $shipment->estimated_arrival->format('M d, Y H:i') }}</span>
                <p class="text-[10px] text-slate-400 mt-1">Expected: {{ $shipment->expected_arrival->format('M d, H:i') }}</p>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block mb-1">Cargo & Weight</span>
                <span class="font-bold text-slate-800 line-clamp-1">{{ $shipment->cargo_type }}</span>
                <p class="text-[10px] text-slate-400 mt-1">{{ number_format($shipment->weight_kg, 1) }} kg • {{ number_format($shipment->volume_cbm, 1) }} CBM</p>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block mb-1">Consignee</span>
                <span class="font-bold text-slate-800">{{ $shipment->customer->name }}</span>
                <p class="text-[10px] text-slate-400 mt-1">{{ $shipment->customer->company_name ?? $shipment->customer->email }}</p>
            </div>
        </div>

        @if($shipment->delay_reason)
        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
            <div>
                <strong>Delay Advisory:</strong> {{ $shipment->delay_reason }}
            </div>
        </div>
        @endif
    </div>

    <!-- Multi-Currency Financial Ledger Card (Soft Light Style) -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Multi-Currency Financial Ledger</h3>
                <p class="text-[11px] text-slate-400">
                    Base billing: <span class="font-bold text-slate-700 font-mono">{{ $shipment->currency_code }}</span> • Active view: <span class="font-bold text-blue-600 font-mono">{{ $activeCurrencyCode }}</span>
                </p>
            </div>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                1 USD = {{ $currencyService->getCurrency($activeCurrencyCode)?->exchange_rate_to_usd ?? 1 }} {{ $activeCurrencyCode }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Freight Cost</span>
                <span class="font-bold text-slate-900 font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->shipping_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">Base: {{ $currencyService->format($shipment->shipping_cost, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Declared Value</span>
                <span class="font-bold text-slate-900 font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->declared_value, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">Base: {{ $currencyService->format($shipment->declared_value, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Insurance Premium</span>
                <span class="font-bold text-slate-900 font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->insurance_fee, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">Base: {{ $currencyService->format($shipment->insurance_fee, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-xl bg-blue-50/60 border border-blue-200">
                <span class="text-[10px] uppercase font-bold text-blue-700 block mb-0.5">Total Payable</span>
                <span class="font-extrabold text-blue-700 font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->total_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-slate-500 font-mono mt-0.5">Base: {{ $currencyService->format($shipment->total_cost, $shipment->currency_code) }}</span>
            </div>
        </div>
    </div>

    <!-- Interactive Leaflet World Map (Clean Light Tiles) -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Worldwide Route & Position</h3>
                <p class="text-[11px] text-slate-400">
                    Current: <strong class="text-slate-700">{{ $shipment->current_location_name ?: 'En Route' }}</strong>
                </p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Origin</span>
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Destination</span>
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Vessel</span>
            </div>
        </div>

        <div id="shipmentDetailMap" class="w-full h-96 rounded-xl border border-slate-200 overflow-hidden z-0"></div>
    </div>

    <!-- Operations & SLA Control Panel -->
    @can('update', $shipment)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Milestone Transition Form -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Status Progression</h3>
            <p class="text-[11px] text-slate-400">Advance lifecycle milestone. Current: <strong>{{ $shipment->current_status }}</strong></p>

            @if($shipment->isTerminal())
            <div class="p-3 rounded-xl bg-slate-50 text-xs text-slate-500 border border-slate-200">
                Consignment is in a terminal state ({{ $shipment->current_status }}) and cannot transition further.
            </div>
            @else
            <form action="{{ route('shipments.status', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Target Milestone</label>
                    <select name="status" required class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500">
                        @foreach(\App\Models\Shipment::VALID_TRANSITIONS[$shipment->current_status] ?? [] as $next)
                            <option value="{{ $next }}">{{ $next }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Notes</label>
                    <input type="text" name="notes" placeholder="Log details..." class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-xs transition">
                    Transition Status
                </button>
            </form>
            @endif
        </div>

        <!-- Add Checkpoint Form -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Append Checkpoint</h3>
            <p class="text-[11px] text-slate-400">Appends telemetry coordinate with external weather integration.</p>

            <form action="{{ route('shipments.tracking.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status</label>
                        <select name="status" required class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500">
                            <option value="IN_TRANSIT">IN TRANSIT</option>
                            <option value="PORT_ARRIVAL">PORT ARRIVAL</option>
                            <option value="CUSTOMS_CLEARANCE">CUSTOMS CLEARANCE</option>
                            <option value="DEPARTED">DEPARTED</option>
                            <option value="DELIVERED">DELIVERED</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Location Name</label>
                        <input type="text" name="location_name" required placeholder="Port or sea waypoint" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" placeholder="e.g. 1.2902" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" placeholder="e.g. 103.8519" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Description</label>
                    <input type="text" name="description" required placeholder="Checkpoint notes..." class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none">
                </div>

                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs shadow-xs transition">
                    Save Checkpoint
                </button>
            </form>
        </div>
    </div>
    @endcan

    <!-- Exceptions SLA Center -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Incident & Exception Management</h3>
                <p class="text-[11px] text-slate-400">Operational holds, storm reroutes, and biosecurity issues</p>
            </div>

            @can('update', $shipment)
            <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.toggle('hidden')" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-semibold transition">
                + Raise Exception
            </button>
            @endcan
        </div>

        <!-- Hidden Drawer to Raise Exception -->
        <div id="raiseExceptionDrawer" class="hidden p-4 rounded-xl bg-slate-50 border border-red-200 space-y-3">
            <h4 class="text-xs font-bold uppercase text-red-700">Log New Incident Exception</h4>
            <form action="{{ route('shipments.exceptions.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Exception Type</label>
                        <select name="exception_type" required class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl">
                            <option value="CUSTOMS_HOLD">CUSTOMS HOLD</option>
                            <option value="WEATHER_DELAY">WEATHER DELAY</option>
                            <option value="PORT_CONGESTION">PORT CONGESTION</option>
                            <option value="DAMAGED">DAMAGED CARGO</option>
                            <option value="MISROUTED">MISROUTED</option>
                            <option value="DOCUMENTATION_ISSUE">DOCUMENTATION ISSUE</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Severity</label>
                        <select name="severity" required class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl">
                            <option value="LOW">LOW (0 - 6h)</option>
                            <option value="MEDIUM" selected>MEDIUM (6 - 24h)</option>
                            <option value="HIGH">HIGH (> 24h)</option>
                            <option value="CRITICAL">CRITICAL</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Delay Impact (Hours)</label>
                        <input type="number" name="delay_impact_hours" min="0" value="12" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Root Cause</label>
                    <textarea name="root_cause" required rows="2" placeholder="Incident explanation..." class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.add('hidden')" class="px-3 py-1 text-xs text-slate-500">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-red-600 text-white font-semibold text-xs">Save Incident</button>
                </div>
            </form>
        </div>

        <!-- Exceptions List -->
        <div class="space-y-2">
            @forelse($shipment->exceptions as $exc)
            <div class="p-3.5 rounded-xl {{ $exc->isOpen() ? 'bg-red-50/60 border border-red-200' : 'bg-slate-50 border border-slate-200' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900">{{ $exc->exception_type }}</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold {{ $exc->isOpen() ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $exc->status }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-mono">{{ $exc->severity }}</span>
                    </div>
                    <p class="text-slate-700 mt-1">{{ $exc->root_cause }}</p>
                    @if($exc->resolution_notes)
                    <p class="text-[11px] text-emerald-700 mt-1">Resolution: {{ $exc->resolution_notes }}</p>
                    @endif
                </div>

                @if($exc->isOpen() && Auth::user()->canManageShipments())
                <form action="{{ route('exceptions.resolve', $exc->id) }}" method="POST" class="flex items-center gap-2 shrink-0">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="resolution_notes" required placeholder="Resolution summary..." class="px-2.5 py-1 text-xs bg-white border border-slate-200 rounded-lg">
                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 text-white font-semibold text-xs">
                        Resolve
                    </button>
                </form>
                @endif
            </div>
            @empty
            <p class="text-xs text-slate-400 text-center py-2">No active exceptions logged for this consignment.</p>
            @endforelse
        </div>
    </div>

    <!-- Timeline & Manifest -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Checkpoint Timeline -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm lg:col-span-2 space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Tracking Timeline</h3>

            <div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @forelse($shipment->trackingEvents as $ev)
                <div class="relative">
                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px]
                        {{ $ev->is_exception ? 'bg-red-500 text-white' : 'bg-blue-600 text-white' }}">
                        <i class="fa-solid fa-check text-[9px]"></i>
                    </div>

                    <div class="p-3.5 rounded-xl {{ $ev->is_exception ? 'bg-red-50/50 border border-red-200' : 'bg-slate-50 border border-slate-100' }}">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="text-xs font-bold text-slate-900">{{ $ev->status }} • {{ $ev->location_name }}</span>
                            <span class="text-[10px] font-mono text-slate-400">{{ $ev->event_time->format('M d, H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-600">{{ $ev->description }}</p>
                        @if($ev->weather_summary)
                        <span class="inline-block mt-1 text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                            {{ $ev->weather_summary }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">No checkpoints recorded.</p>
                @endforelse
            </div>
        </div>

        <!-- Manifest Items -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Manifest Items</h3>

            <div class="space-y-2">
                @forelse($shipment->items as $item)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="font-bold text-slate-800">{{ $item->item_description }}</div>
                    <div class="grid grid-cols-2 gap-1 mt-1 text-[11px] text-slate-500">
                        <div>Qty: <strong>{{ $item->quantity }}</strong></div>
                        <div>Weight: <strong>{{ number_format($item->weight_kg, 1) }} kg</strong></div>
                        <div>Package: {{ $item->package_type }}</div>
                        <div>Value: {{ $currencyService->format($item->declared_value, $shipment->currency_code) }}</div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Consolidated freight container.</p>
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

        const centerPoint = mapData[mapData.length - 1] || mapData[0];
        const map = L.map('shipmentDetailMap', {
            center: [centerPoint.lat, centerPoint.lng],
            zoom: 3,
            minZoom: 2,
            maxZoom: 16,
            worldCopyJump: true
        });

        // CartoDB Positron (Clean Light Soft Map Tiles)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        const latLngs = [];

        mapData.forEach(function (pt) {
            latLngs.push([pt.lat, pt.lng]);

            let color = '#3b82f6';
            if (pt.type === 'origin') color = '#10b981';
            else if (pt.type === 'destination') color = '#2563eb';
            else if (pt.type === 'current') color = '#f59e0b';
            else if (pt.type === 'exception') color = '#ef4444';

            const marker = L.circleMarker([pt.lat, pt.lng], {
                radius: 7,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            let popupContent = `<b>${pt.title}</b><br>${pt.location}`;
            if (pt.time) popupContent += `<br><span style="color:#64748b;font-size:10px;">Time: ${pt.time}</span>`;
            if (pt.weather) popupContent += `<br><span style="color:#0284c7;font-size:10px;">Weather: ${pt.weather}</span>`;
            marker.bindPopup(popupContent);

            if (pt.type === 'current') marker.openPopup();
        });

        if (latLngs.length > 1) {
            const polyline = L.polyline(latLngs, {
                color: '#3b82f6',
                weight: 3,
                opacity: 0.8,
                dashArray: '5, 8'
            }).addTo(map);

            map.fitBounds(polyline.getBounds(), { padding: [30, 30] });
        }
    });
</script>
@endpush
