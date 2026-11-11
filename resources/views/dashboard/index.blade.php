@extends('layouts.app')

@section('title', 'Operations Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Control Console</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-bold border border-blue-200 uppercase">
                    {{ Auth::check() ? Auth::user()->role : 'Public' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-0.5">
                @if(Auth::check() && Auth::user()->isCustomer())
                    Welcome, {{ Auth::user()->company_name ?: Auth::user()->name }}
                @else
                    Operations Dashboard
                @endif
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                @if(Auth::check() && Auth::user()->isCustomer())
                    Monitor your international cargo consignments, delivery ETAs, milestone timelines, and live oceanic routing.
                @else
                    Overview of global shipment visibility, exception management, and multi-currency billing across worldwide trade routes.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(Auth::check() && Auth::user()->canManageShipments())
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Shipment</span>
            </a>
            @endif

            <a href="#live-tracker" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 shadow-xs transition">
                <i class="fa-solid fa-earth-americas text-blue-500 text-xs"></i>
                <span>Live Tracker</span>
            </a>
        </div>
    </div>

    <!-- 6 KPI Metric Cards (Soft Minimal SaaS Style) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Shipments -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ Auth::check() && Auth::user()->isCustomer() ? 'My Cargo' : 'Total' }}</span>
                <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-box"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ $totalShipments }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Active consignments</span>
        </div>

        <!-- In Transit -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">In Transit</span>
                <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-ship"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-blue-600 font-mono">{{ $inTransitCount }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Voyages underway</span>
        </div>

        <!-- Delivered -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Delivered</span>
                <div class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-600 font-mono">{{ $deliveredCount }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Completed & signed</span>
        </div>

        <!-- Delayed -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Delayed</span>
                <div class="w-6 h-6 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-600 font-mono">{{ $delayedCount }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">ETA revised</span>
        </div>

        <!-- Open Exceptions -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-red-600">Incidents</span>
                <div class="w-6 h-6 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-red-600 font-mono">{{ $openExceptionsCount }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Exceptions open</span>
        </div>

        <!-- On-Time Rate KPI -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600">On-Time KPI</span>
                <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ $onTimeRate }}%</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Avg: {{ $avgDelayHours }}h delay</span>
        </div>
    </div>

    <!-- =================================================================== -->
    <!-- INTEGRATED LIVE TRACKER SECTION (Directly inside Dashboard) -->
    <!-- =================================================================== -->
    <div id="live-tracker" class="rounded-2xl bg-white border border-slate-200/90 shadow-sm overflow-hidden scroll-mt-20">
        <!-- Tracker Header Bar & Search Form -->
        <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-200/80 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm shadow-blue-500/20">
                        <i class="fa-solid fa-satellite-dish"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Live Global Consignment Tracker</h2>
                        <p class="text-xs text-slate-500">Real-time GPS telemetry, oceanic waypoint routing, and verified milestone progression.</p>
                    </div>
                </div>

                @if($trackedShipment)
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-medium">Tracking:</span>
                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-blue-600 shadow-2xs">
                        {{ $trackedShipment->shipment_number }}
                    </span>
                </div>
                @endif
            </div>

            <!-- Tracking Number Search Bar -->
            <form action="{{ route('dashboard') }}#live-tracker" method="GET" class="flex flex-col sm:flex-row gap-2 max-w-2xl">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i class="fa-solid fa-barcode text-xs"></i>
                    </span>
                    <input type="text" name="track" value="{{ $trackedNumber ?: ($trackedShipment?->shipment_number ?? '') }}"
                           placeholder="Enter consignment tracking number (e.g. CT-2026-EU4091)..."
                           class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-800 font-mono text-xs placeholder-slate-400 focus:outline-none focus:border-blue-500 transition">
                </div>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Track on Map</span>
                </button>
            </form>

            <!-- Quick Demo Tracking Chips -->
            <div class="flex flex-wrap items-center gap-2 text-xs pt-0.5">
                <span class="text-[11px] text-slate-400 font-medium">Quick Select:</span>
                @foreach($quickDemoShipments as $qs)
                <a href="{{ route('dashboard', ['track' => $qs->shipment_number]) }}#live-tracker"
                   class="px-2.5 py-1 rounded-lg text-[11px] font-mono transition border
                   {{ ($trackedShipment && $trackedShipment->id === $qs->id) ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-2xs' : 'bg-white hover:bg-slate-100 text-slate-600 border-slate-200' }}">
                    {{ $qs->shipment_number }} ({{ $qs->origin->code }} &rarr; {{ $qs->destination->code }})
                </a>
                @endforeach
            </div>
        </div>

        @if($trackedShipment)
        <!-- Relative Map Frame with Compass & GPS Locate Button Overlay (Shopee Style) -->
        <div class="relative w-full h-[440px] z-0">
            <!-- Compass North Overlay (Shopee Style) -->
            <div class="absolute top-3 left-3 z-[400] bg-white/95 backdrop-blur-xs p-1.5 rounded-full shadow-md border border-slate-200/90 flex items-center justify-center w-10 h-10 select-none hover:bg-slate-50 transition cursor-pointer" title="Arah Utara (North)">
                <div class="relative w-6 h-6 flex items-center justify-center">
                    <div class="w-1 h-3 bg-rose-500 rounded-t-sm absolute top-0"></div>
                    <div class="w-1 h-3 bg-slate-300 rounded-b-sm absolute bottom-0"></div>
                    <span class="text-[8px] font-black text-rose-600 absolute -top-1">U</span>
                </div>
            </div>

            <!-- GPS Re-Center Target Button (Shopee Style) -->
            <button type="button" id="recenterDashboardMapBtn" class="absolute bottom-4 right-4 z-[400] bg-white/95 backdrop-blur-xs w-11 h-11 rounded-full shadow-lg border border-slate-200/90 flex items-center justify-center text-slate-700 hover:text-rose-600 hover:scale-105 active:scale-95 transition" title="Pusatkan ke Posisi Kargo">
                <i class="fa-solid fa-crosshairs text-lg"></i>
            </button>

            <!-- Map Div -->
            <div id="dashboardTrackerMap" class="w-full h-full"></div>
        </div>

        <!-- Integrated Shopee / SPX Delivery Status Card (Bottom of Map) -->
        <div class="p-5 sm:p-6 bg-white border-t border-slate-200/90 space-y-5">
            <!-- Estimasi Tiba Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-teal-600 block mb-0.5">
                        <i class="fa-regular fa-calendar-check mr-1"></i> Estimasi Pengiriman Global
                    </span>
                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-800">
                        Estimasi tiba: {{ $trackedShipment->estimated_arrival->format('d M') }} – {{ $trackedShipment->estimated_arrival->copy()->addDays(2)->format('d M Y') }}
                    </h3>
                </div>
                <div class="flex items-center gap-2">
                    @if($trackedShipment->delay_status === 'DELAYED')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Terlambat (+{{ $trackedShipment->delay_hours }} jam)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Tepat Waktu (On Schedule)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Shopee 3-Step Milestone Stepper -->
            <div class="py-2">
                <div class="relative flex items-center justify-between max-w-2xl mx-auto px-4 sm:px-10">
                    <!-- Base gray bar -->
                    <div class="absolute left-10 right-10 top-5 h-0.5 bg-slate-200 -z-0"></div>
                    <!-- Active green fill bar -->
                    @php
                        $stepperPct = '15%';
                        if ($trackedShipment->current_status === 'DELIVERED') {
                            $stepperPct = '100%';
                        } elseif ($trackedShipment->current_status === 'IN_TRANSIT') {
                            $stepperPct = '50%';
                        } elseif (in_array($trackedShipment->current_status, ['PICKED_UP', 'BOOKED'])) {
                            $stepperPct = '25%';
                        }
                    @endphp
                    <div class="absolute left-10 top-5 h-0.5 bg-emerald-500 -z-0 transition-all duration-500" style="width: {{ $stepperPct }};"></div>

                    <!-- Step 1: Sedang Dikirim -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ in_array($trackedShipment->current_status, ['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED']) ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                            <i class="fa-solid fa-box-archive"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ in_array($trackedShipment->current_status, ['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED']) ? 'text-emerald-700' : 'text-slate-400' }}">
                            Sedang Dikirim
                        </span>
                        <span class="text-[10px] text-slate-400 max-w-[90px] truncate">{{ $trackedShipment->origin->city }}</span>
                    </div>

                    <!-- Step 2: Menuju Tujuan -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ in_array($trackedShipment->current_status, ['IN_TRANSIT', 'DELIVERED']) ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                            <i class="fa-solid fa-ship"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ in_array($trackedShipment->current_status, ['IN_TRANSIT', 'DELIVERED']) ? 'text-emerald-700' : 'text-slate-400' }}">
                            Menuju Tujuan
                        </span>
                        <span class="text-[10px] text-slate-400">Dalam Pelayaran</span>
                    </div>

                    <!-- Step 3: Pesanan Tiba -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ $trackedShipment->current_status === 'DELIVERED' ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ $trackedShipment->current_status === 'DELIVERED' ? 'text-emerald-700' : 'text-slate-400' }}">
                            Pesanan Tiba
                        </span>
                        <span class="text-[10px] text-slate-400 max-w-[90px] truncate">{{ $trackedShipment->destination->city }}</span>
                    </div>
                </div>
            </div>

            <!-- Garansi Tiba Badge (Shopee Style) -->
            <div class="p-3.5 rounded-xl bg-teal-50/80 border border-teal-200/90 text-xs text-teal-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-teal-500 text-white flex items-center justify-center text-xs flex-shrink-0">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <strong class="font-semibold text-teal-950">Garansi Tiba Global:</strong> Jaminan SLA ketepatan waktu pengiriman internasional CargoTrace.
                    </div>
                </div>
                <span class="text-[11px] font-bold text-teal-700 bg-white px-2.5 py-1 rounded-md border border-teal-200 shadow-2xs whitespace-nowrap self-start sm:self-auto">
                    Verified SLA
                </span>
            </div>

            <!-- Kurir & Resi Card with 1-Click Copy -->
            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-blue-600 text-xl">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-900">{{ $trackedShipment->carrier->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold uppercase">{{ $trackedShipment->carrier->service_type }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-mono text-xs font-bold text-slate-700" id="dashTrackingSpan">{{ $trackedShipment->shipment_number }}</span>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $trackedShipment->shipment_number }}'); this.querySelector('span').innerText='Tersalin!'; setTimeout(() => this.querySelector('span').innerText='Salin', 2000);"
                                    class="text-[11px] text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition">
                                <i class="fa-regular fa-copy"></i>
                                <span>Salin</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500">Rute: <strong class="text-slate-800">{{ $trackedShipment->origin->city }} &rarr; {{ $trackedShipment->destination->city }}</strong></span>
                    <a href="{{ route('shipments.show', $trackedShipment->id) }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-blue-600 text-xs font-semibold shadow-2xs transition flex items-center gap-1">
                        <span>Lihat Rincian</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Verified Checkpoints Timeline -->
            @if(count($trackedShipment->trackingEvents) > 0)
            <div class="pt-2">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Timeline Checkpoint Terkini</h4>
                <div class="relative pl-6 space-y-3 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @foreach($trackedShipment->trackingEvents->take(4) as $ev)
                    <div class="relative text-xs">
                        <div class="absolute -left-6 top-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px]
                            {{ $ev->is_exception ? 'bg-red-500 text-white' : 'bg-blue-600 text-white' }}">
                            <i class="fa-solid fa-check text-[9px]"></i>
                        </div>
                        <div class="p-3 rounded-xl {{ $ev->is_exception ? 'bg-red-50/60 border border-red-200' : 'bg-slate-50 border border-slate-100' }}">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="font-bold text-slate-900">{{ $ev->status }} &bull; {{ $ev->location_name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $ev->event_time->format('M d, H:i') }}</span>
                            </div>
                            <p class="text-slate-600 text-[11px]">{{ $ev->description }}</p>
                            @if($ev->weather_summary)
                            <span class="inline-block mt-1 text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                ☀️ Cuaca: {{ $ev->weather_summary }}
                            </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @elseif($trackedNumber)
        <div class="p-8 text-center text-slate-500 space-y-2">
            <i class="fa-solid fa-magnifying-glass text-2xl text-slate-300"></i>
            <h3 class="text-sm font-bold text-slate-800">No Consignment Found</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">
                Could not find any consignment matching "<span class="font-mono text-slate-700">{{ $trackedNumber }}</span>". Please verify the consignment code.
            </p>
        </div>
        @endif
    </div>

    <!-- Active Exceptions Banner (For Admin & Operator) -->
    @if((!Auth::check() || !Auth::user()->isCustomer()) && count($activeExceptions) > 0)
    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2 font-bold text-amber-800">
                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                <span>Active Exceptions Requiring SLA Follow-up ({{ count($activeExceptions) }})</span>
            </div>
            <span class="text-[10px] text-amber-700">Immediate attention</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
            @foreach($activeExceptions as $exc)
            <div class="p-3 bg-white rounded-xl border border-amber-200/70 flex items-center justify-between gap-3 shadow-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 font-mono">{{ $exc->shipment->shipment_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">
                            {{ $exc->exception_type }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-1">{{ $exc->root_cause }}</p>
                </div>
                <a href="{{ route('shipments.show', $exc->shipment_id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-blue-600 hover:text-blue-800 shrink-0">
                    Inspect &rarr;
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Visual Charts (For Admin & Operator) -->
    @if(!Auth::check() || !Auth::user()->isCustomer())
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Doughnut Chart -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4">Shipments by Status</h3>
                <div class="relative h-52 flex items-center justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 text-center mt-3">Distribution across shipment lifecycle</p>
        </div>

        <!-- Carrier Bar Chart -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Volume by Carrier</h3>
                    <span class="text-[11px] text-slate-400">Total Load</span>
                </div>
                <div class="relative h-52">
                    <canvas id="carrierChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 text-center mt-3">Active container bookings per shipping line</p>
        </div>
    </div>
    @endif

    <!-- Recent Shipments Table (Matching User Screenshot) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">
                    {{ Auth::check() && Auth::user()->isCustomer() ? 'My Consignments Registry' : 'Recent Consignments' }}
                </h3>
                <p class="text-[11px] text-slate-400">
                    {{ Auth::check() && Auth::user()->isCustomer() ? 'Your registered cargo shipments and arrival schedules' : 'Latest active trade movements' }}
                </p>
            </div>
            <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                View All &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-semibold text-slate-500">
                        <th class="py-3 px-5">Shipment</th>
                        <th class="py-3 px-4">Route</th>
                        <th class="py-3 px-4">Carrier</th>
                        <th class="py-3 px-4">Cargo</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Cost ({{ $activeCurrencyCode }})</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($recentShipments as $s)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3.5 px-5 font-bold text-slate-900 font-mono">
                            <a href="{{ route('shipments.show', $s->id) }}" class="hover:text-blue-600">
                                {{ $s->shipment_number }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            {{ $s->origin->city }} &rarr; {{ $s->destination->city }}
                        </td>
                        <td class="py-3.5 px-4">
                            {{ $s->carrier->name }}
                        </td>
                        <td class="py-3.5 px-4 line-clamp-1 max-w-xs">
                            {{ $s->cargo_type }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($s->current_status === 'DELIVERED')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Delivered
                                </span>
                            @elseif($s->current_status === 'IN_TRANSIT')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    In Transit
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $s->current_status }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                            {{ $currencyService->convertAndFormat($s->total_cost, $s->currency_code, $activeCurrencyCode) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('dashboard', ['track' => $s->shipment_number]) }}#live-tracker"
                                   class="p-1.5 text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition" title="Lacak di Peta Dashboard">
                                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                                </a>
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-lg transition" title="Inspect">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No shipments registered.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Shopee / SPX Style Callout Bubble & Pin styles inside Dashboard */
    .vehicle-callout-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        transform: translate(-50%, -100%);
        pointer-events: auto;
        cursor: pointer;
    }
    .shopee-bubble {
        position: relative;
        background: #ffffff;
        color: #e11d48;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(225, 29, 72, 0.25);
        white-space: nowrap;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .shopee-bubble::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 6px solid #ffffff;
    }
    .vehicle-badge {
        position: relative;
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border: 2.5px solid #ffffff;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f59e0b;
        font-size: 18px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.35);
        transition: transform 0.2s ease;
    }
    .vehicle-badge:hover {
        transform: scale(1.1);
    }
    .vehicle-radar {
        position: absolute;
        inset: -4px;
        border-radius: 16px;
        background: rgba(245, 158, 11, 0.35);
        animation: ping 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        z-index: -1;
    }
    .port-pin {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 11px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // -------------------------------------------------------------
        // 1. Dashboard Leaflet Map Initialization (Shopee / SPX Style)
        // -------------------------------------------------------------
        @if($trackedShipment && count($mapPoints) > 0)
        const mapData = {!! json_encode($mapPoints) !!};

        let currentPos = null;
        mapData.forEach(pt => {
            if (pt.type === 'current') currentPos = pt;
        });
        if (!currentPos) {
            currentPos = mapData[mapData.length - 1] || mapData[0];
        }

        const map = L.map('dashboardTrackerMap', {
            center: [currentPos.lat, currentPos.lng],
            zoom: 4,
            minZoom: 2,
            maxZoom: 18,
            worldCopyJump: true
        });

        // OpenStreetMap Standard Tiles (No API key required, natural green land & blue sea)
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        const latLngs = [];
        let vehicleMarker = null;

        const shipmentStatus = "{{ $trackedShipment->current_status }}";
        let calloutStatus = "Memproses pesanan";
        if (shipmentStatus === 'IN_TRANSIT') {
            calloutStatus = "Sedang Berlayar (In Transit)";
        } else if (shipmentStatus === 'DELIVERED') {
            calloutStatus = "Pesanan Tiba";
        } else if (shipmentStatus === 'PICKED_UP') {
            calloutStatus = "Kargo Dimuat";
        } else if (shipmentStatus === 'BOOKED') {
            calloutStatus = "Siap Diberangkatkan";
        }

        mapData.forEach(function (pt) {
            latLngs.push([pt.lat, pt.lng]);

            if (pt.type === 'current') {
                const vehicleHtml = `
                    <div class="vehicle-callout-wrapper">
                        <div class="shopee-bubble">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 inline-block animate-ping"></span>
                            <span>${calloutStatus}</span>
                        </div>
                        <div class="vehicle-badge">
                            <div class="vehicle-radar"></div>
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

                vehicleMarker = L.marker([pt.lat, pt.lng], { icon: vehicleIcon, zIndexOffset: 1000 }).addTo(map);
                vehicleMarker.bindPopup(`
                    <div style="font-size: 12px;">
                        <strong style="color: #0f172a;">${pt.title}</strong><br>
                        <span style="color: #64748b;">${pt.location || ''}</span>
                        ${pt.weather ? `<br><span style="color: #0284c7; font-weight: 600; font-size: 11px;">☀️ Cuaca: ${pt.weather}</span>` : ''}
                    </div>
                `);

            } else if (pt.type === 'origin') {
                const originIcon = L.divIcon({
                    className: 'custom-origin-div',
                    html: `<div class="port-pin bg-emerald-600"><i class="fa-solid fa-anchor"></i></div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
                L.marker([pt.lat, pt.lng], { icon: originIcon }).addTo(map)
                    .bindPopup(`<b>Pelabuhan Asal:</b> ${pt.location}`);

            } else if (pt.type === 'destination') {
                const destIcon = L.divIcon({
                    className: 'custom-dest-div',
                    html: `<div class="port-pin bg-blue-600"><i class="fa-solid fa-location-dot"></i></div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
                L.marker([pt.lat, pt.lng], { icon: destIcon }).addTo(map)
                    .bindPopup(`<b>Pelabuhan Tujuan:</b> ${pt.location}`);

            } else {
                const isExc = pt.type === 'exception';
                const wpIcon = L.circleMarker([pt.lat, pt.lng], {
                    radius: isExc ? 7 : 5,
                    fillColor: isExc ? '#ef4444' : '#3b82f6',
                    color: '#ffffff',
                    weight: 2,
                    fillOpacity: 0.9
                }).addTo(map);
                wpIcon.bindPopup(`<b>${pt.title}</b><br>${pt.location}`);
            }
        });

        // Shopee / Logistics Style Polyline
        if (latLngs.length > 1) {
            L.polyline(latLngs, {
                color: '#ea580c',
                weight: 5,
                opacity: 0.85
            }).addTo(map);

            L.polyline(latLngs, {
                color: '#ffffff',
                weight: 2,
                opacity: 0.9,
                dashArray: '6, 8'
            }).addTo(map);

            map.fitBounds(L.polyline(latLngs).getBounds(), { padding: [40, 40] });
        }

        // GPS Re-Center Button Click
        const recenterBtn = document.getElementById('recenterDashboardMapBtn');
        if (recenterBtn && currentPos) {
            recenterBtn.addEventListener('click', function () {
                map.flyTo([currentPos.lat, currentPos.lng], 6, {
                    animate: true,
                    duration: 1.2
                });
                if (vehicleMarker) {
                    vehicleMarker.openPopup();
                }
            });
        }
        @endif

        // -------------------------------------------------------------
        // 2. Chart.js (For Admin & Operator)
        // -------------------------------------------------------------
        @if(!Auth::check() || !Auth::user()->isCustomer())
        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            new Chart(statusCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Created', 'Booked', 'Picked Up', 'In Transit', 'Delivered'],
                    datasets: [{
                        data: [
                            {{ $statusCounts['CREATED'] ?? 0 }},
                            {{ $statusCounts['BOOKED'] ?? 0 }},
                            {{ $statusCounts['PICKED_UP'] ?? 0 }},
                            {{ $statusCounts['IN_TRANSIT'] ?? 0 }},
                            {{ $statusCounts['DELIVERED'] ?? 0 }}
                        ],
                        backgroundColor: ['#cbd5e1', '#fde68a', '#c7d2fe', '#93c5fd', '#a7f3d0'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#64748b', font: { size: 10, family: 'Plus Jakarta Sans' }, padding: 8 }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        const carrierCanvas = document.getElementById('carrierChart');
        if (carrierCanvas) {
            new Chart(carrierCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_keys($carrierCounts)) !!},
                    datasets: [{
                        label: 'Consignments',
                        data: {!! json_encode(array_values($carrierCounts)) !!},
                        backgroundColor: '#60a5fa',
                        borderRadius: 6,
                        hoverBackgroundColor: '#3b82f6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#94a3b8', stepSize: 1, font: { size: 10 } },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: { color: '#64748b', font: { size: 10, family: 'Plus Jakarta Sans' } },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
        @endif
    });
</script>
@endpush
