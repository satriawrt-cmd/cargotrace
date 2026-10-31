@extends('layouts.app')

@section('title', 'Shipments Registry')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <i class="fa-solid fa-boxes-stacked text-brand-400"></i>
                <span>Shipment Registry</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Displaying international consignments. Active currency: <strong class="text-brand-400 font-mono">{{ $activeCurrencyCode }}</strong>
            </p>
        </div>

        @if(Auth::user()->canManageShipments())
        <a href="{{ route('shipments.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-lg shadow-brand-500/25 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> New Global Shipment
        </a>
        @endif
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl">
        <form action="{{ route('shipments.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search text -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Search Consignment</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Shipment #, cargo, city..."
                               class="w-full pl-9 pr-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-brand-500">
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        <option value="">All Statuses</option>
                        <option value="CREATED" {{ ($filters['status'] ?? '') === 'CREATED' ? 'selected' : '' }}>CREATED</option>
                        <option value="BOOKED" {{ ($filters['status'] ?? '') === 'BOOKED' ? 'selected' : '' }}>BOOKED</option>
                        <option value="PICKED_UP" {{ ($filters['status'] ?? '') === 'PICKED_UP' ? 'selected' : '' }}>PICKED UP</option>
                        <option value="IN_TRANSIT" {{ ($filters['status'] ?? '') === 'IN_TRANSIT' ? 'selected' : '' }}>IN TRANSIT</option>
                        <option value="DELIVERED" {{ ($filters['status'] ?? '') === 'DELIVERED' ? 'selected' : '' }}>DELIVERED</option>
                        <option value="CANCELLED" {{ ($filters['status'] ?? '') === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                    </select>
                </div>

                <!-- Carrier Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Carrier</label>
                    <select name="carrier_id" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        <option value="">All Carriers</option>
                        @foreach($carriers as $c)
                            <option value="{{ $c->id }}" {{ ($filters['carrier_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Delay Status Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Delay Indicator</label>
                    <select name="delay_status" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        <option value="">All ETA Conditions</option>
                        <option value="ON_TIME" {{ ($filters['delay_status'] ?? '') === 'ON_TIME' ? 'selected' : '' }}>On Schedule</option>
                        <option value="DELAYED" {{ ($filters['delay_status'] ?? '') === 'DELAYED' ? 'selected' : '' }}>Delayed Only</option>
                    </select>
                </div>
            </div>

            <!-- Second row: Port origin / destination & submit button -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Origin Port</label>
                    <select name="origin_id" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        <option value="">Any Origin Hub</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ ($filters['origin_id'] ?? '') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->city }}, {{ $loc->country }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Destination Port</label>
                    <select name="destination_id" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-brand-500">
                        <option value="">Any Destination Hub</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ ($filters['destination_id'] ?? '') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->city }}, {{ $loc->country }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs border border-slate-700 transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('shipments.index') }}" class="py-2 px-3 rounded-xl bg-slate-950 hover:bg-slate-800 text-slate-400 text-xs border border-slate-800 transition" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Shipments Table -->
    <div class="rounded-3xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Shipment Number</th>
                        <th class="py-3.5 px-4">Client / Shipper</th>
                        <th class="py-3.5 px-4">Carrier</th>
                        <th class="py-3.5 px-4">Origin & Destination</th>
                        <th class="py-3.5 px-4">Cargo & Weight</th>
                        <th class="py-3.5 px-4">Status & Delay</th>
                        <th class="py-3.5 px-4 text-right">Cost ({{ $activeCurrencyCode }})</th>
                        <th class="py-3.5 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($shipments as $shipment)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-4 px-4 font-mono font-bold text-white">
                            <a href="{{ route('shipments.show', $shipment->id) }}" class="text-brand-400 hover:text-brand-300 hover:underline">
                                {{ $shipment->shipment_number }}
                            </a>
                            <div class="text-[10px] text-slate-500 font-sans font-normal">
                                Booked: {{ $shipment->created_at->format('M d, Y') }}
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="font-medium text-slate-200">{{ $shipment->customer->name }}</span>
                            @if($shipment->customer->company_name)
                            <div class="text-[10px] text-slate-500">{{ $shipment->customer->company_name }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <span class="font-semibold text-slate-200">{{ $shipment->carrier->name }}</span>
                            <div class="text-[10px] text-slate-500">{{ $shipment->carrier->service_type }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-200">{{ $shipment->origin->city }}</span>
                                <i class="fa-solid fa-arrow-right text-[9px] text-slate-600"></i>
                                <span class="font-bold text-slate-200">{{ $shipment->destination->city }}</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-mono">
                                {{ $shipment->origin->continent }} → {{ $shipment->destination->continent }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="line-clamp-1 max-w-xs text-slate-200 font-medium">{{ $shipment->cargo_type }}</div>
                            <span class="text-[10px] text-slate-500">{{ number_format($shipment->weight_kg, 1) }} kg • {{ number_format($shipment->volume_cbm, 1) }} CBM</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $shipment->current_status === 'DELIVERED' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                                    {{ $shipment->current_status === 'IN_TRANSIT' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : '' }}
                                    {{ in_array($shipment->current_status, ['CREATED', 'BOOKED', 'PICKED_UP']) ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : '' }}
                                    {{ $shipment->current_status === 'CANCELLED' ? 'bg-slate-700 text-slate-400' : '' }}">
                                    {{ str_replace('_', ' ', $shipment->current_status) }}
                                </span>
                                @if($shipment->delay_status === 'DELAYED')
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-red-500/20 text-red-400 border border-red-500/30">
                                    +{{ $shipment->delay_hours }}h
                                </span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-500">ETA: {{ $shipment->estimated_arrival->format('M d, Y') }}</span>
                        </td>
                        <td class="py-4 px-4 text-right font-mono font-bold text-white">
                            {{ $currencyService->convertAndFormat($shipment->total_cost, $shipment->currency_code, $activeCurrencyCode) }}
                            <div class="text-[9px] text-slate-500 font-normal">
                                Orig: {{ $currencyService->format($shipment->total_cost, $shipment->currency_code) }}
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('shipments.show', $shipment->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="View Details">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                @can('update', $shipment)
                                <a href="{{ route('shipments.edit', $shipment->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                @endcan
                                @can('delete', $shipment)
                                <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete shipment {{ $shipment->shipment_number }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 transition" title="Delete">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <span class="text-sm font-semibold text-slate-400">No shipments found matching your filters.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Server-side Pagination -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            {{ $shipments->links() }}
        </div>
    </div>
</div>
@endsection
