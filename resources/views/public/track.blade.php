@extends('layouts.app')

@section('title', 'Global Public Tracking')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header Title & Centered Search Form (Ocean Maritime Style) -->
    <div class="text-center space-y-3 pt-2">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0F172A]">Track Global Consignments</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto">
            Real-time visibility across maritime shipping lines and international trade routes with verified waypoint telemetry.
        </p>

        <!-- Search Bar -->
        <form action="{{ route('public.track') }}" method="GET" class="flex flex-col sm:flex-row items-center justify-center gap-2 max-w-xl mx-auto pt-2">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-barcode text-sm"></i>
                </span>
                <input type="text" name="shipment_number" value="{{ $shipmentNumber ?? '' }}"
                       placeholder="Enter tracking number (e.g. CT-2026-EU4091)"
                       class="w-full pl-10 pr-4 py-3 bg-white border border-slate-300 rounded-2xl text-[#0F172A] font-mono text-xs placeholder-slate-400 focus:outline-none focus:border-[#1E3A8A] focus:ring-2 focus:ring-[#1E3A8A]/20 transition shadow-xs">
            </div>
            <button type="submit" class="w-full sm:w-auto px-7 py-3 bg-[#0B132B] hover:bg-[#15253E] text-white font-semibold text-xs rounded-2xl shadow-sm transition flex items-center justify-center gap-2 border border-[#1E293B] shrink-0">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Track</span>
            </button>
        </form>

        <!-- Quick Demo Tracker Pills (Dark Ocean Navy Pills) -->
        <div class="flex flex-wrap items-center justify-center gap-2 text-xs pt-1">
            <span class="text-slate-500 text-xs font-semibold">Quick Select:</span>
            @foreach($quickDemoShipments ?? [] as $qs)
            <a href="{{ route('public.track', ['shipment_number' => $qs->shipment_number]) }}"
               class="px-3.5 py-1.5 rounded-full border text-xs font-mono transition shadow-2xs flex items-center gap-1.5
               {{ ($shipment && $shipment->id === $qs->id) ? 'bg-[#0B132B] text-white border-2 border-[#38BDF8] font-bold shadow-sm' : 'bg-[#15253E] hover:bg-[#1E3A8A] text-slate-200 border-slate-700/60' }}">
                <span>{{ $qs->shipment_number }}</span>
                <span class="text-[10px] text-sky-300">({{ $qs->origin->code }} &rarr; {{ $qs->destination->code }})</span>
            </a>
            @endforeach
        </div>
    </div>

    @if($shipment)
    <!-- Maritime Route Stepper / Progress Bar with Sailing Cargo Ship -->
    @php
        $statusSequence = ['CREATED' => 10, 'BOOKED' => 25, 'PICKED_UP' => 50, 'IN_TRANSIT' => 75, 'DELIVERED' => 100];
        $currProgress = $statusSequence[$shipment->current_status] ?? 25;
    @endphp
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-xs sm:text-sm font-bold text-[#0F172A] px-1">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#1E3A8A] ring-4 ring-sky-100"></span>
                <span>{{ $shipment->origin->city }} ({{ $shipment->origin->country }})</span>
            </div>
            <div class="flex items-center gap-2">
                <span>{{ $shipment->destination->city }} ({{ $shipment->destination->country }})</span>
                <span class="w-2.5 h-2.5 rounded-full bg-[#38BDF8] ring-4 ring-sky-100"></span>
            </div>
        </div>

        <!-- Segmented Maritime Bar with Ship Icon -->
        <div class="relative pt-6 pb-2">
            <!-- Ship Icon positioned along the progress bar -->
            <div class="absolute top-0 -translate-x-1/2 transition-all duration-700 z-10 flex flex-col items-center" style="left: {{ $currProgress }}%;">
                <div class="px-2 py-0.5 rounded-md bg-[#0B132B] text-white text-[10px] font-bold shadow-md border border-sky-400 whitespace-nowrap mb-1 flex items-center gap-1">
                    <i class="fa-solid fa-ship text-[#38BDF8] text-[9px]"></i>
                    <span>{{ $shipment->current_status === 'DELIVERED' ? 'Delivered' : ($shipment->current_status === 'IN_TRANSIT' ? 'In Transit' : 'Cargo Staged') }}</span>
                </div>
                <div class="text-[#0B132B] text-base leading-none">
                    <i class="fa-solid fa-ship"></i>
                </div>
            </div>

            <!-- Track Bar -->
            <div class="w-full bg-[#15253E] h-3.5 rounded-full relative overflow-hidden border border-slate-700/60 flex">
                <div class="bg-gradient-to-r from-[#1E3A8A] via-[#2563EB] to-[#38BDF8] h-full rounded-full transition-all duration-700" style="width: {{ $currProgress }}%;"></div>
                <!-- Segment separators -->
                <div class="absolute inset-0 flex justify-between pointer-events-none px-4">
                    <span class="w-0.5 h-full bg-white/20"></span>
                    <span class="w-0.5 h-full bg-white/20"></span>
                    <span class="w-0.5 h-full bg-white/20"></span>
                    <span class="w-0.5 h-full bg-white/20"></span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between text-[11px] text-slate-500 font-mono pt-1 px-1">
            <span>Departure: {{ $shipment->expected_departure->format('d M Y') }}</span>
            <span class="text-xs font-semibold text-[#1E3A8A]">
                {{ $shipment->delay_status === 'DELAYED' ? '⚠️ Delayed (+'.$shipment->delay_hours.'h)' : '✓ On Schedule' }}
            </span>
            <span>Est. Arrival: {{ $shipment->estimated_arrival->format('d M Y') }}</span>
        </div>
    </div>

    <!-- The Signature Two Ocean Blue Cards Side-by-Side (Ocean Maritime Theme) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Card: Shipment Summary -->
        <div class="bg-gradient-to-br from-[#163B65] via-[#153B64] to-[#0F2A48] border border-[#255285] rounded-3xl p-6 sm:p-7 text-white shadow-xl space-y-5 relative overflow-hidden flex flex-col justify-between">
            <!-- Decorative World Map SVG Graphic in background -->
            <div class="absolute -right-6 -bottom-6 w-72 h-44 opacity-20 pointer-events-none text-sky-300">
                <svg viewBox="0 0 1000 500" fill="currentColor">
                    <path d="M150,150 Q200,100 250,150 T350,150 T450,200 T350,300 T200,320 T150,250 Z M600,100 Q700,80 800,120 T900,200 T850,300 T700,280 T600,200 Z M750,320 Q850,330 880,400 T800,450 T750,380 Z"/>
                </svg>
            </div>

            <div class="space-y-4 relative z-10">
                <div class="flex items-start justify-between gap-3 border-b border-white/10 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-white tracking-wide">Consignment Summary</h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="text-xl sm:text-2xl font-black font-mono tracking-wider text-white">{{ $shipment->shipment_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#38BDF8] text-[#082F49] shadow-xs">
                                {{ $shipment->current_status }}
                            </span>
                            @if($shipment->delay_status === 'DELAYED')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-amber-950">
                                Delayed (+{{ $shipment->delay_hours }}h)
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400 text-emerald-950">
                                On Schedule
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center text-sky-300 shrink-0">
                        <i class="fa-solid fa-box-open text-lg"></i>
                    </div>
                </div>

                <!-- Detailed Key-Value Specs in Ocean Card -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-1">
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-3">
                        <span class="text-[10px] uppercase font-bold text-sky-300 block mb-0.5">Carrier & Service</span>
                        <p class="font-bold text-white text-sm">{{ $shipment->carrier->name }}</p>
                        <span class="text-[11px] text-sky-200/80">{{ $shipment->carrier->service_type }} • {{ $shipment->carrier->code }}</span>
                    </div>

                    <div class="bg-white/5 border border-white/10 rounded-2xl p-3">
                        <span class="text-[10px] uppercase font-bold text-sky-300 block mb-0.5">Cargo & Manifest</span>
                        <p class="font-bold text-white text-sm truncate">{{ $shipment->cargo_type }}</p>
                        <span class="text-[11px] text-sky-200/80">{{ number_format($shipment->weight_kg, 1) }} kg • {{ number_format($shipment->volume_cbm, 1) }} CBM</span>
                    </div>

                    <div class="bg-white/5 border border-white/10 rounded-2xl p-3">
                        <span class="text-[10px] uppercase font-bold text-sky-300 block mb-0.5">Port of Origin</span>
                        <p class="font-bold text-white">{{ $shipment->origin->city }} ({{ $shipment->origin->code }})</p>
                        <span class="text-[11px] text-sky-200/80">{{ $shipment->origin->name }}, {{ $shipment->origin->country }}</span>
                    </div>

                    <div class="bg-white/5 border border-white/10 rounded-2xl p-3">
                        <span class="text-[10px] uppercase font-bold text-sky-300 block mb-0.5">Destination Port</span>
                        <p class="font-bold text-white">{{ $shipment->destination->city }} ({{ $shipment->destination->code }})</p>
                        <span class="text-[11px] text-sky-200/80">{{ $shipment->destination->name }}, {{ $shipment->destination->country }}</span>
                    </div>
                </div>

                <!-- Position Alert & Resi Copy -->
                <div class="p-3.5 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#38BDF8] animate-ping shrink-0"></span>
                        <span class="truncate">Current Position: <strong class="text-sky-200 font-semibold">{{ $shipment->current_location_name ?: 'En Route (Open Sea)' }}</strong></span>
                    </div>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $shipment->shipment_number }}'); this.querySelector('span').innerText='Copied!'; setTimeout(() => this.querySelector('span').innerText='Copy Code', 2000);"
                            class="px-2.5 py-1 rounded-lg bg-[#38BDF8] hover:bg-[#7DD3FC] text-[#082F49] font-bold text-[11px] transition shrink-0 flex items-center gap-1 shadow-xs">
                        <i class="fa-regular fa-copy"></i>
                        <span>Copy Code</span>
                    </button>
                </div>
            </div>

            <!-- Guarantee SLA Tag -->
            <div class="pt-3 border-t border-white/10 flex items-center justify-between text-xs text-sky-200/80 relative z-10">
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-[#38BDF8]"></i>
                    <span>CargoTrace Maritime SLA Guarantee</span>
                </div>
                <span class="font-mono text-[11px] text-sky-300">Verified Waypoint Telemetry</span>
            </div>
        </div>

        <!-- Right Card: Checkpoint Timeline -->
        <div class="bg-gradient-to-br from-[#163B65] via-[#153B64] to-[#0F2A48] border border-[#255285] rounded-3xl p-6 sm:p-7 text-white shadow-xl space-y-4 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-[#38BDF8]">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h2 class="text-lg font-bold text-white tracking-wide">Tracking Timeline</h2>
                    </div>
                    <span class="text-xs text-sky-300 font-mono">{{ count($shipment->trackingEvents) }} Checkpoints</span>
                </div>

                <!-- Vertical Checkpoint List with Ocean Style Glowing Points -->
                <div class="relative pl-7 space-y-4 before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-white/20 max-h-[360px] overflow-y-auto pr-2">
                    @forelse($shipment->trackingEvents as $ev)
                    <div class="relative group">
                        <!-- Glowing Blue Circle Checkmark Icon -->
                        <div class="absolute -left-7 top-1 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shadow-md transition
                            {{ $ev->is_exception ? 'bg-amber-400 text-amber-950 shadow-amber-400/30' : 'bg-[#38BDF8] text-[#082F49] shadow-[#38BDF8]/40 ring-4 ring-[#163B65]' }}">
                            <i class="fa-solid fa-check text-[10px]"></i>
                        </div>

                        <div class="p-3.5 rounded-2xl {{ $ev->is_exception ? 'bg-amber-500/15 border border-amber-400/30' : 'bg-white/5 border border-white/10' }} text-xs space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-white text-sm">{{ $ev->status }} • {{ $ev->location_name }}</span>
                                <span class="text-[11px] font-mono text-sky-300 shrink-0">{{ $ev->event_time->format('d M, H:i') }}</span>
                            </div>
                            <p class="text-sky-100/90 leading-relaxed text-xs">{{ $ev->description }}</p>
                            @if($ev->weather_summary)
                            <div class="pt-1">
                                <span class="inline-flex items-center gap-1 text-[11px] text-sky-200 bg-white/10 px-2 py-0.5 rounded-md border border-white/15">
                                    <i class="fa-solid fa-cloud-sun text-xs text-sky-300"></i>
                                    <span>{{ $ev->weather_summary }} ({{ $ev->temperature_c }}°C)</span>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-sky-300">No verified checkpoints recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-white/10 flex items-center justify-between text-xs text-sky-200/80">
                <span>Telemetry Status: Automated Waypoint Logging</span>
                <span class="font-mono text-sky-300">Real-Time Sync</span>
            </div>
        </div>
    </div>

    <!-- Interactive World Map (Ocean Maritime Frame) -->
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <!-- Map Header Bar with Compass & Target status -->
        <div class="px-6 py-4 bg-[#0B132B] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#1E293B]">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-[#1E3A8A] text-[#38BDF8] border border-sky-400/30 flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Global Route Telemetry Map</h3>
                    <p class="text-xs text-slate-400">
                        Vessel Position: <strong class="text-sky-300">{{ $shipment->current_location_name ?: 'En Route (Open Sea)' }}</strong>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5 text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-[#1E3A8A] ring-2 ring-sky-400"></span> Origin</span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-[#38BDF8] ring-2 ring-white"></span> Destination</span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 ring-2 ring-white"></span> Vessel Position</span>
            </div>
        </div>

        <!-- Relative Map Frame with GPS Locate Button Overlay -->
        <div class="relative w-full h-[430px] z-0">
            <!-- GPS Re-Center Target Button -->
            <button type="button" id="recenterPublicMapBtn" class="absolute bottom-4 right-4 z-[400] bg-[#0B132B] hover:bg-[#15253E] text-white w-11 h-11 rounded-full shadow-lg border border-slate-700 flex items-center justify-center hover:scale-105 active:scale-95 transition" title="Center on Vessel Position">
                <i class="fa-solid fa-crosshairs text-base text-[#38BDF8]"></i>
            </button>

            <!-- Map Div -->
            <div id="publicWorldMap" class="w-full h-full"></div>
        </div>
    </div>
    @elseif($shipmentNumber)
    <div class="p-10 text-center rounded-3xl bg-white border border-slate-200 max-w-md mx-auto shadow-sm">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto text-xl mb-3">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h3 class="text-base font-bold text-[#0F172A]">Consignment Not Found</h3>
        <p class="text-xs text-slate-500 mt-1">
            No consignment found with tracking number "<span class="font-mono text-[#0F172A] font-bold">{{ $shipmentNumber }}</span>".
        </p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if($shipment)
<style>
    /* Ocean Maritime Style Callout Bubble & Pin styles */
    .vehicle-callout-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        transform: translate(-50%, -100%);
        pointer-events: auto;
        cursor: pointer;
    }
    .ocean-bubble {
        position: relative;
        background: #0B132B;
        color: #38BDF8;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        box-shadow: 0 4px 14px rgba(11, 19, 43, 0.4);
        border: 1px solid #1E3A8A;
        white-space: nowrap;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .ocean-bubble::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #0B132B;
    }
    .vehicle-badge-ocean {
        position: relative;
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #1E3A8A 0%, #0B132B 100%);
        border: 2.5px solid #38BDF8;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 18px;
        box-shadow: 0 8px 18px rgba(30, 58, 138, 0.45);
        transition: transform 0.2s ease;
    }
    .vehicle-badge-ocean:hover {
        transform: scale(1.1);
    }
    .vehicle-radar-ocean {
        position: absolute;
        inset: -4px;
        border-radius: 16px;
        background: rgba(56, 189, 248, 0.4);
        animation: ping 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        z-index: -1;
    }
    .port-pin-ocean {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 11px;
        box-shadow: 0 4px 10px rgba(11, 19, 43, 0.3);
    }
</style>

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
            zoom: 4,
            minZoom: 2,
            maxZoom: 18,
            worldCopyJump: true
        });

        // OpenStreetMap Standard Tiles
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
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

        // Ocean Maritime Polyline (Deep Royal Navy with Cyan Dash Center)
        L.polyline(routePoints, {
            color: '#1E3A8A',
            weight: 5,
            opacity: 0.9
        }).addTo(map);

        L.polyline(routePoints, {
            color: '#38BDF8',
            weight: 2,
            opacity: 0.95,
            dashArray: '6, 8'
        }).addTo(map);

        // Origin Pin
        const originIcon = L.divIcon({
            className: 'custom-origin-div',
            html: `<div class="port-pin-ocean bg-[#0B132B]"><i class="fa-solid fa-anchor"></i></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
        L.marker([originLat, originLng], { icon: originIcon }).addTo(map)
            .bindPopup("<b>Port of Origin:</b> {{ $shipment->origin->name }}");

        // Destination Pin
        const destIcon = L.divIcon({
            className: 'custom-dest-div',
            html: `<div class="port-pin-ocean bg-[#38BDF8] text-[#0B132B]"><i class="fa-solid fa-location-dot"></i></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
        L.marker([destLat, destLng], { icon: destIcon }).addTo(map)
            .bindPopup("<b>Destination Port:</b> {{ $shipment->destination->name }}");

        // Current Vessel Marker
        const shipmentStatus = "{{ $shipment->current_status }}";
        let calloutText = "Processing Order";
        if (shipmentStatus === 'IN_TRANSIT') {
            calloutText = "Sailing In Transit";
        } else if (shipmentStatus === 'DELIVERED') {
            calloutText = "Delivered";
        } else if (shipmentStatus === 'PICKED_UP') {
            calloutText = "Cargo Loaded";
        }

        const vehicleHtml = `
            <div class="vehicle-callout-wrapper">
                <div class="ocean-bubble">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#38BDF8] inline-block animate-ping"></span>
                    <span>${calloutText}</span>
                </div>
                <div class="vehicle-badge-ocean">
                    <div class="vehicle-radar-ocean"></div>
                    <i class="fa-solid fa-ship"></i>
                </div>
            </div>
        `;

        const vehicleIcon = L.divIcon({
            className: 'custom-vehicle-div',
            html: vehicleHtml,
            iconSize: [140, 80],
            iconAnchor: [70, 75]
        });

        const vehicleMarker = L.marker([currLat, currLng], { icon: vehicleIcon, zIndexOffset: 1000 }).addTo(map);
        vehicleMarker.bindPopup("<b>Current Position:</b> {{ $shipment->current_location_name ?: 'En Route' }}");

        map.fitBounds(L.polyline(routePoints).getBounds(), { padding: [40, 40] });

        // Re-center button
        const recenterBtn = document.getElementById('recenterPublicMapBtn');
        if (recenterBtn) {
            recenterBtn.addEventListener('click', function () {
                map.flyTo([currLat, currLng], 6, {
                    animate: true,
                    duration: 1.2
                });
                vehicleMarker.openPopup();
            });
        }
    });
</script>
@endif
@endpush