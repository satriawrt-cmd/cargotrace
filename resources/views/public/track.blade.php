@extends('layouts.app')

@section('title', 'Global Public Tracking')

@section('content')
<div class="space-y-6">
    <!-- Clean Soft Hero Search (Minimal SaaS Style) -->
    <div class="p-8 sm:p-10 rounded-2xl bg-white border border-slate-200/90 shadow-sm max-w-4xl mx-auto text-center space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center mx-auto text-xl shadow-xs">
            <i class="fa-solid fa-earth-americas"></i>
        </div>

        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Track Global Consignments</h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto mt-1">
                Real-time visibility across international trade routes with verified waypoint telemetry and status progression.
            </p>
        </div>

        <!-- Search Form -->
        <form action="{{ route('public.track') }}" method="GET" class="flex flex-col sm:flex-row gap-2 max-w-lg mx-auto pt-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-barcode text-xs"></i>
                </span>
                <input type="text" name="shipment_number" value="{{ $shipmentNumber ?? '' }}"
                       placeholder="Enter tracking number (e.g. CT-2026-EU4091)"
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Track</span>
            </button>
        </form>

        <!-- Quick Demo Tracker Pills (Soft Calm Badges) -->
        <div class="flex flex-wrap items-center justify-center gap-2 text-xs pt-1">
            <span class="text-slate-400 text-[11px]">Sample Shipments:</span>
            <a href="{{ route('public.track', ['shipment_number' => 'CT-2026-EU4091']) }}" class="px-2.5 py-1 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-mono text-[11px] transition">
                CT-2026-EU4091 (Asia → Europe)
            </a>
            <a href="{{ route('public.track', ['shipment_number' => 'CT-2026-OC8821']) }}" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 font-mono text-[11px] transition">
                CT-2026-OC8821 (Delayed)
            </a>
            <a href="{{ route('public.track', ['shipment_number' => 'CT-2026-SA1029']) }}" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-mono text-[11px] transition">
                CT-2026-SA1029 (Delivered)
            </a>
        </div>
    </div>

    @if($shipment)
    <!-- Shipment Overview Card -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm max-w-4xl mx-auto space-y-6">
        <!-- Number & Badges -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Consignment Number</span>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 font-mono mt-0.5">{{ $shipment->shipment_number }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Carrier: <strong class="text-slate-800">{{ $shipment->carrier->name }}</strong> • Cargo: {{ $shipment->cargo_type }}
                </p>
            </div>

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

        <!-- Lifecycle Stepper -->
        @php
            $steps = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
            $currentIndex = array_search($shipment->current_status, $steps);
            if ($currentIndex === false) $currentIndex = 0;
        @endphp
        <div class="py-2">
            <div class="grid grid-cols-5 gap-2 text-center">
                @foreach($steps as $idx => $step)
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition
                        {{ $idx <= $currentIndex ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                        @if($idx < $currentIndex)
                            <i class="fa-solid fa-check text-[10px]"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>
                    <span class="text-[10px] font-semibold mt-1.5 {{ $idx <= $currentIndex ? 'text-slate-900' : 'text-slate-400' }}">
                        {{ str_replace('_', ' ', $step) }}
                    </span>
                </div>
                @endforeach
            </div>
            <div class="w-full bg-slate-100 h-1 rounded-full mt-3 overflow-hidden">
                <div class="bg-blue-600 h-full rounded-full transition-all duration-300"
                     style="width: {{ (($currentIndex + 0.5) / count($steps)) * 100 }}%"></div>
            </div>
        </div>

        <!-- Route Origin & Destination -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Origin Port</span>
                <span class="font-bold text-slate-900 text-sm">{{ $shipment->origin->city }}</span>
                <p class="text-slate-500 text-[11px]">{{ $shipment->origin->name }}, {{ $shipment->origin->country }}</p>
                <span class="text-[10px] text-slate-400 mt-1 block">Departed: {{ $shipment->expected_departure->format('M d, Y') }}</span>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Destination Port</span>
                <span class="font-bold text-slate-900 text-sm">{{ $shipment->destination->city }}</span>
                <p class="text-slate-500 text-[11px]">{{ $shipment->destination->name }}, {{ $shipment->destination->country }}</p>
                <span class="text-[10px] text-slate-400 mt-1 block">ETA: {{ $shipment->estimated_arrival->format('M d, Y') }}</span>
            </div>
        </div>

        <!-- Interactive World Map (CartoDB Positron Light Tiles) -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Voyage Trajectory</h3>
                <span class="text-[11px] text-slate-400">Position: {{ $shipment->current_location_name ?: 'En Route' }}</span>
            </div>
            <div id="publicWorldMap" class="w-full h-80 rounded-xl border border-slate-200 overflow-hidden z-0"></div>
        </div>

        <!-- Timeline Checkpoints -->
        <div class="space-y-3 pt-2">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Verified Checkpoints</h3>

            <div class="relative pl-6 space-y-3 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @forelse($shipment->trackingEvents as $ev)
                <div class="relative">
                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px]
                        {{ $ev->is_exception ? 'bg-red-500 text-white' : 'bg-blue-600 text-white' }}">
                        <i class="fa-solid fa-check text-[9px]"></i>
                    </div>

                    <div class="p-3 rounded-xl {{ $ev->is_exception ? 'bg-red-50/60 border border-red-200' : 'bg-slate-50 border border-slate-100' }} text-xs">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="font-bold text-slate-900">{{ $ev->status }} • {{ $ev->location_name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $ev->event_time->format('M d, H:i') }}</span>
                        </div>
                        <p class="text-slate-600">{{ $ev->description }}</p>
                        @if($ev->weather_summary)
                        <span class="inline-block mt-1 text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                            {{ $ev->weather_summary }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">No checkpoints logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>
    @elseif($shipmentNumber)
    <div class="p-8 text-center rounded-2xl bg-white border border-slate-200 max-w-md mx-auto shadow-sm">
        <i class="fa-solid fa-magnifying-glass text-xl text-slate-400 mb-2 block"></i>
        <h3 class="text-sm font-bold text-slate-800">No Consignment Found</h3>
        <p class="text-xs text-slate-500 mt-1">
            Could not find any consignment matching "<span class="font-mono text-slate-700">{{ $shipmentNumber }}</span>".
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

        const map = L.map('publicWorldMap', {
            center: [currLat, currLng],
            zoom: 3,
            minZoom: 2,
            maxZoom: 14,
            worldCopyJump: true
        });

        // CartoDB Positron (Clean Light Soft Map Tiles)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        }).addTo(map);

        const routePoints = [
            [originLat, originLng],
            @foreach($shipment->trackingEvents as $ev)
                @if($ev->latitude && $ev->longitude)
                    [{{ $ev->latitude }}, {{ $ev->longitude }}],
                @endif
            @endforeach
            [destLat, destLng]
        ];

        const polyline = L.polyline(routePoints, {
            color: '#3b82f6',
            weight: 3,
            opacity: 0.8,
            dashArray: '5, 8'
        }).addTo(map);

        // Origin
        L.circleMarker([originLat, originLng], {
            radius: 6,
            fillColor: '#10b981',
            color: '#ffffff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map).bindPopup("<b>Origin:</b> {{ $shipment->origin->name }}");

        // Destination
        L.circleMarker([destLat, destLng], {
            radius: 6,
            fillColor: '#2563eb',
            color: '#ffffff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map).bindPopup("<b>Destination:</b> {{ $shipment->destination->name }}");

        // Current Vessel Position
        L.circleMarker([currLat, currLng], {
            radius: 8,
            fillColor: '#f59e0b',
            color: '#ffffff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map).bindPopup("<b>Current Position:</b> {{ $shipment->current_location_name ?: 'En Route' }}").openPopup();

        map.fitBounds(polyline.getBounds(), { padding: [30, 30] });
    });
</script>
@endif
@endpush
