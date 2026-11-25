@extends('layouts.app')

@section('title', 'Pelacakan Publik Global')

@section('content')
<div class="space-y-6">
    <!-- Clean Soft Hero Search (Minimal SaaS Style) -->
    <div class="p-8 sm:p-10 rounded-2xl bg-white border border-slate-200/90 shadow-sm max-w-4xl mx-auto text-center space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center mx-auto text-xl shadow-xs">
            <i class="fa-solid fa-earth-americas"></i>
        </div>

        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Lacak Pengiriman Global</h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto mt-1">
                Visibilitas waktu nyata di seluruh rute perdagangan internasional dengan telemetri titik pemeriksaan dan perkembangan status.
            </p>
        </div>

        <!-- Search Form -->
        <form action="{{ route('public.track') }}" method="GET" class="flex flex-col sm:flex-row gap-2 max-w-lg mx-auto pt-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <i class="fa-solid fa-barcode text-xs"></i>
                </span>
                <input type="text" name="shipment_number" value="{{ $shipmentNumber ?? '' }}"
                       placeholder="Masukkan nomor pelacakan (contoh: CT-2026-EU4091)"
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Lacak</span>
            </button>
        </form>

        <!-- Quick Demo Tracker Pills (Soft Calm Badges) -->
        <div class="flex flex-wrap items-center justify-center gap-2 text-xs pt-1">
            <span class="text-slate-400 text-[11px]">Pilihan Cepat:</span>
            @foreach($quickDemoShipments ?? [] as $qs)
            <a href="{{ route('public.track', ['shipment_number' => $qs->shipment_number]) }}"
               class="px-2.5 py-1 rounded-lg border text-[11px] font-mono transition
               {{ ($shipment && $shipment->id === $qs->id) ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}">
                {{ $qs->shipment_number }} ({{ $qs->origin->code }} &rarr; {{ $qs->destination->code }})
            </a>
            @endforeach
        </div>
    </div>

    @if($shipment)
    <!-- Shipment Overview Card -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm max-w-4xl mx-auto space-y-6">
        <!-- Number & Badges -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor Konsinyasi</span>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900 font-mono mt-0.5">{{ $shipment->shipment_number }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Operator: <strong class="text-slate-800">{{ $shipment->carrier->name }}</strong> • Kargo: {{ $shipment->cargo_type }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if($shipment->current_status === 'DELIVERED')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Terkirim
                    </span>
                @elseif($shipment->current_status === 'IN_TRANSIT')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        Dalam Perjalanan
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $shipment->current_status === 'CREATED' ? 'Dibuat' : ($shipment->current_status === 'BOOKED' ? 'Terjadwal' : ($shipment->current_status === 'PICKED_UP' ? 'Diambil' : str_replace('_', ' ', $shipment->current_status))) }}
                    </span>
                @endif

                @if($shipment->delay_status === 'DELAYED')
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                    Terlambat (+{{ $shipment->delay_hours }} jam)
                </span>
                @else
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Tepat Waktu
                </span>
                @endif
            </div>
        </div>

        <!-- Lifecycle Stepper -->
        @php
            $steps = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
            $stepLabels = [
                'CREATED' => 'Dibuat',
                'BOOKED' => 'Terjadwal',
                'PICKED_UP' => 'Diambil',
                'IN_TRANSIT' => 'Dalam Perjalanan',
                'DELIVERED' => 'Terkirim'
            ];
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
                        {{ $stepLabels[$step] ?? $step }}
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
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Pelabuhan Asal</span>
                <span class="font-bold text-slate-900 text-sm">{{ $shipment->origin->city }}</span>
                <p class="text-slate-500 text-[11px]">{{ $shipment->origin->name }}, {{ $shipment->origin->country }}</p>
                <span class="text-[10px] text-slate-400 mt-1 block">Berangkat: {{ $shipment->expected_departure->format('d M Y') }}</span>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Pelabuhan Tujuan</span>
                <span class="font-bold text-slate-900 text-sm">{{ $shipment->destination->city }}</span>
                <p class="text-slate-500 text-[11px]">{{ $shipment->destination->name }}, {{ $shipment->destination->country }}</p>
                <span class="text-[10px] text-slate-400 mt-1 block">Estimasi Tiba (ETA): {{ $shipment->estimated_arrival->format('d M Y') }}</span>
            </div>
        </div>

        <!-- Interactive World Map (Shopee / Logistics Style Container) -->
        <div class="rounded-2xl bg-white border border-slate-200/90 shadow-sm overflow-hidden space-y-0">
            <!-- Map Header Bar with Compass & Target status -->
            <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-sm shadow-2xs">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </span>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pelacakan Rute & Posisi Terkini</h3>
                        <p class="text-[11px] text-slate-400">
                            Lokasi Kargo: <strong class="text-slate-700">{{ $shipment->current_location_name ?: 'En Route' }}</strong>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-100"></span> Asal</span>
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-blue-600 ring-2 ring-blue-100"></span> Tujuan</span>
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-rose-100"></span> Kargo</span>
                </div>
            </div>

            <!-- Relative Map Frame with Compass & GPS Locate Button Overlay (Shopee Style) -->
            <div class="relative w-full h-[400px] z-0">
                <!-- Compass North Overlay (Shopee Style) -->
                <div class="absolute top-3 left-3 z-[400] bg-white/95 backdrop-blur-xs p-1.5 rounded-full shadow-md border border-slate-200/90 flex items-center justify-center w-10 h-10 select-none hover:bg-slate-50 transition cursor-pointer" title="Arah Utara (North)">
                    <div class="relative w-6 h-6 flex items-center justify-center">
                        <div class="w-1 h-3 bg-rose-500 rounded-t-sm absolute top-0"></div>
                        <div class="w-1 h-3 bg-slate-300 rounded-b-sm absolute bottom-0"></div>
                        <span class="text-[8px] font-black text-rose-600 absolute -top-1">U</span>
                    </div>
                </div>

                <!-- GPS Re-Center Target Button (Shopee Style) -->
                <button type="button" id="recenterPublicMapBtn" class="absolute bottom-4 right-4 z-[400] bg-white/95 backdrop-blur-xs w-11 h-11 rounded-full shadow-lg border border-slate-200/90 flex items-center justify-center text-slate-700 hover:text-rose-600 hover:scale-105 active:scale-95 transition" title="Pusatkan ke Posisi Kargo">
                    <i class="fa-solid fa-crosshairs text-lg"></i>
                </button>

                <!-- Map Div -->
                <div id="publicWorldMap" class="w-full h-full"></div>
            </div>

            <!-- Shopee / SPX Delivery Status Card Banner (Under Map) -->
            <div class="p-5 bg-white border-t border-slate-200/90 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-teal-600 block mb-0.5">
                            <i class="fa-regular fa-calendar-check mr-1"></i> Estimasi Pengiriman Global
                        </span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-800">
                            Estimasi tiba: {{ $shipment->estimated_arrival->format('d M') }} – {{ $shipment->estimated_arrival->copy()->addDays(2)->format('d M Y') }}
                        </h2>
                    </div>
                    <div>
                        @if($shipment->delay_status === 'DELAYED')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                Terlambat (+{{ $shipment->delay_hours }} jam)
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
                        <div class="absolute left-10 right-10 top-5 h-0.5 bg-slate-200 -z-0"></div>
                        @php
                            $stepperPct = '15%';
                            if ($shipment->current_status === 'DELIVERED') {
                                $stepperPct = '100%';
                            } elseif ($shipment->current_status === 'IN_TRANSIT') {
                                $stepperPct = '50%';
                            } elseif (in_array($shipment->current_status, ['PICKED_UP', 'BOOKED'])) {
                                $stepperPct = '25%';
                            }
                        @endphp
                        <div class="absolute left-10 top-5 h-0.5 bg-emerald-500 -z-0 transition-all duration-500" style="width: {{ $stepperPct }};"></div>

                        <!-- Step 1: Sedang Dikirim -->
                        <div class="flex flex-col items-center z-10 text-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                                {{ in_array($shipment->current_status, ['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED']) ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                                <i class="fa-solid fa-box-archive"></i>
                            </div>
                            <span class="text-xs font-bold mt-2 {{ in_array($shipment->current_status, ['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED']) ? 'text-emerald-700' : 'text-slate-400' }}">
                                Sedang Dikirim
                            </span>
                            <span class="text-[10px] text-slate-400 max-w-[90px] truncate">{{ $shipment->origin->city }}</span>
                        </div>

                        <!-- Step 2: Menuju Tujuan -->
                        <div class="flex flex-col items-center z-10 text-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                                {{ in_array($shipment->current_status, ['IN_TRANSIT', 'DELIVERED']) ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                                <i class="fa-solid fa-ship"></i>
                            </div>
                            <span class="text-xs font-bold mt-2 {{ in_array($shipment->current_status, ['IN_TRANSIT', 'DELIVERED']) ? 'text-emerald-700' : 'text-slate-400' }}">
                                Menuju Tujuan
                            </span>
                            <span class="text-[10px] text-slate-400">Dalam Pelayaran</span>
                        </div>

                        <!-- Step 3: Pesanan Tiba -->
                        <div class="flex flex-col items-center z-10 text-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                                {{ $shipment->current_status === 'DELIVERED' ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="text-xs font-bold mt-2 {{ $shipment->current_status === 'DELIVERED' ? 'text-emerald-700' : 'text-slate-400' }}">
                                Pesanan Tiba
                            </span>
                            <span class="text-[10px] text-slate-400 max-w-[90px] truncate">{{ $shipment->destination->city }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shopee Garansi Tiba Banner -->
                <div class="p-3.5 rounded-xl bg-teal-50/80 border border-teal-200/90 text-xs text-teal-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-teal-500 text-white flex items-center justify-center text-xs flex-shrink-0">
                            <i class="fa-solid fa-stopwatch"></i>
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
                                <span class="text-xs font-bold text-slate-900">{{ $shipment->carrier->name }}</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold uppercase">{{ $shipment->carrier->service_type }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono text-xs font-bold text-slate-700" id="publicResiSpan">{{ $shipment->shipment_number }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $shipment->shipment_number }}'); this.querySelector('span').innerText='Tersalin!'; setTimeout(() => this.querySelector('span').innerText='Salin', 2000);"
                                        class="text-[11px] text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition">
                                    <i class="fa-regular fa-copy"></i>
                                    <span>Salin</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-500">Rute: <strong class="text-slate-800">{{ $shipment->origin->city }} &rarr; {{ $shipment->destination->city }}</strong></span>
                        @auth
                        <a href="{{ route('shipments.show', $shipment->id) }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-blue-600 text-xs font-semibold shadow-2xs transition flex items-center gap-1">
                            <span>Console Details</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Checkpoints -->
        <div class="space-y-3 pt-2">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Titik Pemeriksaan Terverifikasi</h3>

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
                            <span class="text-[10px] text-slate-400 font-mono">{{ $ev->event_time->format('d M, H:i') }}</span>
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
                <p class="text-xs text-slate-400">Belum ada riwayat titik pemeriksaan tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>
    @elseif($shipmentNumber)
    <div class="p-8 text-center rounded-2xl bg-white border border-slate-200 max-w-md mx-auto shadow-sm">
        <i class="fa-solid fa-magnifying-glass text-xl text-slate-400 mb-2 block"></i>
        <h3 class="text-sm font-bold text-slate-800">Pengiriman Tidak Ditemukan</h3>
        <p class="text-xs text-slate-500 mt-1">
            Tidak ditemukan konsinyasi dengan nomor "<span class="font-mono text-slate-700">{{ $shipmentNumber }}</span>".
        </p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
@if($shipment)
<style>
    /* Shopee / SPX Style Callout Bubble & Pin styles */
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

        // OpenStreetMap Standard Tiles (Natural Green Land & Blue Sea, No API Key)
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

        // Shopee / Logistics Style Polyline (Coral/Orange Glow + Dashed Center)
        L.polyline(routePoints, {
            color: '#ea580c',
            weight: 5,
            opacity: 0.85
        }).addTo(map);

        L.polyline(routePoints, {
            color: '#ffffff',
            weight: 2,
            opacity: 0.9,
            dashArray: '6, 8'
        }).addTo(map);

        // Origin Pin
        const originIcon = L.divIcon({
            className: 'custom-origin-div',
            html: `<div class="port-pin bg-emerald-600"><i class="fa-solid fa-anchor"></i></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
        L.marker([originLat, originLng], { icon: originIcon }).addTo(map)
            .bindPopup("<b>Pelabuhan Asal:</b> {{ $shipment->origin->name }}");

        // Destination Pin
        const destIcon = L.divIcon({
            className: 'custom-dest-div',
            html: `<div class="port-pin bg-blue-600"><i class="fa-solid fa-location-dot"></i></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
        L.marker([destLat, destLng], { icon: destIcon }).addTo(map)
            .bindPopup("<b>Pelabuhan Tujuan:</b> {{ $shipment->destination->name }}");

        // Current Vessel Marker (Shopee Callout Bubble)
        const shipmentStatus = "{{ $shipment->current_status }}";
        let calloutText = "Memproses pesanan";
        if (shipmentStatus === 'IN_TRANSIT') {
            calloutText = "Sedang Berlayar (In Transit)";
        } else if (shipmentStatus === 'DELIVERED') {
            calloutText = "Pesanan Tiba";
        } else if (shipmentStatus === 'PICKED_UP') {
            calloutText = "Kargo Dimuat";
        }

        const vehicleHtml = `
            <div class="vehicle-callout-wrapper">
                <div class="shopee-bubble">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 inline-block animate-ping"></span>
                    <span>${calloutText}</span>
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

        const vehicleMarker = L.marker([currLat, currLng], { icon: vehicleIcon, zIndexOffset: 1000 }).addTo(map);
        vehicleMarker.bindPopup("<b>Posisi Terkini:</b> {{ $shipment->current_location_name ?: 'En Route' }}");

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
