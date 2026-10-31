@extends('layouts.app')

@section('title', 'Worldwide Ports & Hubs')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                <i class="fa-solid fa-earth-americas text-brand-400"></i>
                <span>Worldwide Logistics Hubs & Ports</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Global maritime gateways across all 6 inhabited continents with UN/LOCODE and verified coordinates.
            </p>
        </div>

        @if(Auth::user()->canManageShipments())
        <button type="button" onclick="document.getElementById('addLocationModal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs shadow-lg shadow-brand-500/20 transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add Port Hub
        </button>
        @endif
    </div>

    <!-- Continents Grouping -->
    @php
        $groupedLocations = $locations->groupBy('continent');
    @endphp

    <div class="space-y-8">
        @foreach($groupedLocations as $continent => $locs)
        <div class="space-y-3">
            <div class="flex items-center gap-2 text-sm font-bold text-brand-400 uppercase tracking-wider pb-2 border-b border-slate-800">
                <i class="fa-solid fa-compass"></i>
                <span>{{ $continent }} ({{ $locs->count() }} Ports)</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($locs as $loc)
                <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $loc->country }}</span>
                            <h3 class="text-base font-bold text-white">{{ $loc->city }}</h3>
                            <p class="text-xs text-slate-400">{{ $loc->name }}</p>
                        </div>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-brand-500/10 text-brand-300 border border-brand-500/20">
                            {{ $loc->code }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-400 pt-3 border-t border-slate-800 font-mono">
                        <div>Lat: <span class="text-slate-200">{{ sprintf('%.4f', $loc->latitude) }}</span></div>
                        <div>Lng: <span class="text-slate-200">{{ sprintf('%.4f', $loc->longitude) }}</span></div>
                        <div>Type: <span class="text-slate-300">{{ $loc->type }}</span></div>
                        <div>Zone: <span class="text-slate-300 truncate">{{ $loc->timezone }}</span></div>
                    </div>

                    <div class="pt-2 border-t border-slate-800 flex items-center justify-between text-xs text-slate-500">
                        <span>Voyages:</span>
                        <span class="font-bold text-slate-300 font-mono">
                            {{ $loc->origin_shipments_count ?? 0 }} dep / {{ $loc->destination_shipments_count ?? 0 }} arr
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Location Modal -->
    <div id="addLocationModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-white">Add Worldwide Logistics Port</h3>
                <button type="button" onclick="document.getElementById('addLocationModal').classList.add('hidden')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('locations.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Port / Facility Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Port of Yokohama" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">UN/LOCODE *</label>
                        <input type="text" name="code" required placeholder="e.g. JPYOK" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Type *</label>
                        <select name="type" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                            <option value="SEAPORT">SEAPORT</option>
                            <option value="AIRPORT">AIRPORT</option>
                            <option value="LOGISTICS_HUB">LOGISTICS HUB</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">City *</label>
                        <input type="text" name="city" required placeholder="Yokohama" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Country *</label>
                        <input type="text" name="country" required placeholder="Japan" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Continent *</label>
                    <select name="continent" required class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                        <option value="Asia">Asia</option>
                        <option value="Europe">Europe</option>
                        <option value="North America">North America</option>
                        <option value="South America">South America</option>
                        <option value="Africa">Africa</option>
                        <option value="Oceania">Oceania</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Latitude *</label>
                        <input type="number" step="any" name="latitude" required placeholder="35.4437" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Longitude *</label>
                        <input type="number" step="any" name="longitude" required placeholder="139.6380" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addLocationModal').classList.add('hidden')" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-semibold text-xs">Save Port</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
