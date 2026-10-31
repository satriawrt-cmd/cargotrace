@extends('layouts.app')

@section('title', 'Global Public Shipment Tracking')

@section('content')
<div class="space-y-8">
    <!-- Hero Header & Search -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900 to-brand-950 border border-slate-800 p-8 md:p-12 shadow-2xl">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-brand-500/10 blur-3xl pointer-events-none"></div>

        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-brand-500/10 text-brand-400 border border-brand-500/20 mb-4">
                <i class="fa-solid fa-satellite-dish animate-pulse"></i> Real-time Global Cargo Visibility
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-white mb-3">
                Track Global Shipments <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-cyan-400">Anywhere</span>
            </h1>
            <p class="text-slate-400 text-sm md:text-base mb-6 leading-relaxed">
                Monitor worldwide maritime and multimodal container voyages with millisecond checkpoint logging, live weather at sea, and delay exception tracking across all continents.
            </p>

            <!-- Search Form -->
            <form action="{{ route('public.track') }}" method="GET" class="relative flex flex-col sm:flex-row gap-2 max-w-xl">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <i class="fa-solid fa-barcode text-base"></i>
                    </span>
                    <input type="text" name="shipment_number" value="{{ $shipmentNumber ?? '' }}"
                           placeholder="Enter Tracking Number (e.g. CT-2026-EU4091)"
                           class="w-full pl-11 pr-4 py-3.5 bg-slate-950/90 border border-slate-700 rounded-2xl text-white font-mono text-sm placeholder-slate-500 focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition shadow-inner">
                </div>
                <button type="submit" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 text-white font-semibold text-sm shadow-lg shadow-brand-500/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Track Shipment</span>
                </button>
            </form>

            <!-- Quick Demo Trackers -->
            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-slate-400">
                <span class="font-medium text-slate-500">Demo Worldwide Shipments:</span>
                <a href="{{ route('public.track', ['shipment_number' => 'CT-2026-EU4091']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 font-mono text-brand-300 transition">
                    Asia → Europe (CT-2026-EU4091)
                </a>
                <a href="{{ route('public.track', ['shipment_number' => 'CT-2026-OC8821']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 font-mono text-amber-300 transition">
                    SE Asia → Australia (Delayed)
                </a>
                <a href="{{ route('public.track', ['shipment_number' => 'CT-2026-SA1029']) }}" class="px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 font-mono text-emerald-300 transition">
                    S. America → Europe (Delivered)
                </a>
            </div>
        </div>
    </div>

    @if($shipment)
    <!-- Shipment Overview Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-8 shadow-xl space-y-6">
        <!-- Top Row: Number, Carrier, Status -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Tracking Number</span>
                    <span class="text-xs px-2 py-0.5 rounded bg-brand-500/10 text-brand-400 font-mono border border-brand-500/20">Verified</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-white font-mono mt-0.5">{{ $shipment->shipment_number }}</h2>
                <p class="text-xs text-slate-400 mt-1">
                    Operated by <strong class="text-slate-200">{{ $shipment->carrier->name }}</strong> ({{ $shipment->carrier->code }}) • Cargo: <span class="text-slate-300">{{ $shipment->cargo_type }}</span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Status Badge -->
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider
                    {{ $shipment->current_status === 'DELIVERED' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                    {{ $shipment->current_status === 'IN_TRANSIT' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30 animate-pulse' : '' }}
                    {{ in_array($shipment->current_status, ['CREATED', 'BOOKED', 'PICKED_UP']) ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : '' }}">
                    <i class="fa-solid fa-circle text-[8px] mr-1.5"></i>{{ str_replace('_', ' ', $shipment->current_status) }}
                </span>

                <!-- Delay Indicator -->
                @if($shipment->delay_status === 'DELAYED')
                <span class="px-3 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-red-500/20 text-red-400 border border-red-500/30 flex items-center gap-1.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Delayed (+{{ $shipment->delay_hours }}h)</span>
                </span>
                @else
                <span class="px-3 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <i class="fa-solid fa-clock-check mr-1"></i> On Schedule
                </span>
                @endif
            </div>
        </div>

        <!-- Progress Lifecycle Stepper -->
        @php
            $steps = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
            $currentIndex = array_search($shipment->current_status, $steps);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp
        <div class="py-2">
            <div class="grid grid-cols-5 gap-2 text-center">
                @foreach($steps as $idx => $step)
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition
                        {{ $idx <= $currentIndex ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/25 ring-4 ring-brand-500/10' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                        @if($idx < $currentIndex)
                            <i class="fa-solid fa-check"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <span class="text-[11px] font-semibold mt-2 {{ $idx <= $currentIndex ? 'text-white' : 'text-slate-500' }}">
                        {{ str_replace('_', ' ', $step) }}
                    </span>
                </div>
                @endforeach
            </div>
            <!-- Progress Bar Line -->
            <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-gradient-to-r from-brand-600 to-cyan-400 h-full rounded-full transition-all duration-500"
                     style="width: {{ (($currentIndex + 0.5) / count($steps)) * 100 }}%"></div>
            </div>
        </div>

        <!-- Route Origin & Destination Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <!-- Origin -->
            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-anchor text-base"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Origin Port</span>
                    <h4 class="text-base font-bold text-white">{{ $shipment->origin->name }}</h4>
                    <p class="text-xs text-slate-400">{{ $shipment->origin->city }}, {{ $shipment->origin->country }} ({{ $shipment->origin->code }})</p>
                    <p class="text-[11px] text-slate-500 mt-2">
                        Departed: <strong class="text-slate-300">{{ $shipment->expected_departure->format('M d, Y H:i') }} UTC</strong>
                    </p>
                </div>
            </div>

            <!-- Destination -->
            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-location-dot text-base"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400">Destination Port</span>
                    <h4 class="text-base font-bold text-white">{{ $shipment->destination->name }}</h4>
                    <p class="text-xs text-slate-400">{{ $shipment->destination->city }}, {{ $shipment->destination->country }} ({{ $shipment->destination->code }})</p>
                    <p class="text-[11px] text-slate-500 mt-2">
                        Estimated Arrival: <strong class="text-slate-300">{{ $shipment->estimated_arrival->format('M d, Y H:i') }} UTC</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Interactive Leaflet World Map -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-brand-400"></i> Worldwide Voyage Route & Current Position
                </h3>
                <span class="text-xs text-slate-400 font-mono">OpenStreetMap + Leaflet.js</span>
            </div>
            <div id="publicWorldMap" class="w-full h-96 rounded-2xl border border-slate-800 overflow-hidden shadow-inner z-0"></div>
        </div>

        <!-- Timeline Checkpoints -->
        <div>
            <h3 class="text-sm font-bold text-slate-200 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-timeline text-brand-400"></i> Verified Tracking Timeline
            </h3>

            <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                @forelse($shipment->trackingEvents as $ev)
                <div class="relative group">
                    <!-- Dot -->
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
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-500">No checkpoints recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
    @elseif($shipmentNumber)
    <!-- Not Found State -->
    <div class="p-12 text-center rounded-3xl bg-slate-900 border border-slate-800">
        <div class="w-16 h-16 rounded-2xl bg-red-500/10 text-red-400 border border-red-500/20 flex items-center justify-center mx-auto mb-4 text-2xl">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h3 class="text-lg font-bold text-white">No Shipment Found</h3>
        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
            We could not find any active consignment matching "<span class="text-slate-200 font-mono">{{ $shipmentNumber }}</span>". Please verify your reference number.
        </p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if($shipment)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const originLat = {{ $shipment->origin->latitude }};
        const originLng = {{ $shipment->origin->longitude }};
        const destLat = {{ $shipment->destination->latitude }};
        const destLng = {{ $shipment->destination->longitude }};
        const currLat = {{ $shipment->current_latitude ?? $shipment->origin->latitude }};
        const currLng = {{ $shipment->current_longitude ?? $shipment->origin->longitude }};

        // Initialize world map
        const map = L.map('publicWorldMap', {
            center: [currLat, currLng],
            zoom: 3,
            minZoom: 2,
            maxZoom: 14,
            worldCopyJump: true
        });

        // Dark modern map tile layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        // Coordinates sequence for polyline
        const routePoints = [
            [originLat, originLng],
            @foreach($shipment->trackingEvents as $ev)
                @if($ev->latitude && $ev->longitude)
                    [{{ $ev->latitude }}, {{ $ev->longitude }}],
                @endif
            @endforeach
            [destLat, destLng]
        ];

        // Draw Route Polyline
        const polyline = L.polyline(routePoints, {
            color: '#2563eb',
            weight: 3.5,
            opacity: 0.8,
            dashArray: '6, 8',
            smoothFactor: 1
        }).addTo(map);

        // Origin Marker (Green)
        L.circleMarker([originLat, originLng], {
            radius: 8,
            fillColor: '#10b981',
            color: '#ffffff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map).bindPopup("<b>Origin:</b> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}");

        // Destination Marker (Blue)
        L.circleMarker([destLat, destLng], {
            radius: 8,
            fillColor: '#3b82f6',
            color: '#ffffff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map).bindPopup("<b>Destination:</b> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}");

        // Current Location Pulsing Marker
        const currentIcon = L.divIcon({
            className: 'custom-vessel-marker',
            html: '<div style="background-color: #f59e0b; width: 18px; height: 18px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 0 12px #f59e0b; animation: pulse 1.5s infinite;"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 9]
        });

        L.marker([currLat, currLng], { icon: currentIcon })
            .addTo(map)
            .bindPopup("<b>Current Position:</b> {{ $shipment->current_location_name ?: 'En Route' }}<br>Status: {{ $shipment->current_status }}")
            .openPopup();

        // Fit map bounds to encompass route
        map.fitBounds(polyline.getBounds(), { padding: [40, 40] });
    });
</script>
@endif
@endpush
