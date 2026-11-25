@extends('layouts.app')

@section('title', 'Pengiriman ' . $shipment->shipment_number)

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
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Konsinyasi</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 font-mono font-bold">TERVERIFIKASI</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-mono mt-0.5">{{ $shipment->shipment_number }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('update', $shipment)
            <a href="{{ route('shipments.edit', $shipment->id) }}" class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square text-slate-500 text-[11px]"></i>
                <span>Edit</span>
            </a>
            @endcan

            @can('delete', $shipment)
            <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" onsubmit="return confirm('Hapus permanen pengiriman ini?');" class="inline">
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
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Asal</span>
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
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tujuan</span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ $shipment->destination->city }}</h3>
                    <p class="text-xs text-slate-500">{{ $shipment->destination->country }} ({{ $shipment->destination->code }})</p>
                </div>
            </div>

            <!-- Status & Delay Badge -->
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
                        Terlambat (+{{ $shipment->delay_hours }}j)
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Tepat Waktu
                    </span>
                @endif
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block mb-1">Jadwal Keberangkatan</span>
                <span class="font-bold text-slate-800 font-mono">{{ $shipment->expected_departure->format('d M Y H:i') }}</span>
                @if($shipment->actual_departure)
                <p class="text-[10px] text-emerald-600 mt-1">Aktual: {{ $shipment->actual_departure->format('d M, H:i') }}</p>
                @endif
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block mb-1">Estimasi Tiba (ETA)</span>
                <span class="font-bold text-slate-800 font-mono">{{ $shipment->estimated_arrival->format('d M Y H:i') }}</span>
                <p class="text-[10px] text-slate-400 mt-1">Diharapkan: {{ $shipment->expected_arrival->format('d M, H:i') }}</p>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block mb-1">Kargo & Berat</span>
                <span class="font-bold text-slate-800 line-clamp-1">{{ $shipment->localized_cargo_type }}</span>
                <p class="text-[10px] text-slate-400 mt-1">{{ number_format($shipment->weight_kg, 1) }} kg • {{ number_format($shipment->volume_cbm, 1) }} CBM</p>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block mb-1">Penerima (Consignee)</span>
                <span class="font-bold text-slate-800">{{ $shipment->customer->name }}</span>
                <p class="text-[10px] text-slate-400 mt-1">{{ $shipment->customer->company_name ?? $shipment->customer->email }}</p>
            </div>
        </div>

        @if($shipment->delay_reason)
        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
            <div>
                <strong>Pemberitahuan Keterlambatan:</strong> {{ $shipment->delay_reason }}
            </div>
        </div>
        @endif
    </div>

    <!-- Multi-Currency Financial Ledger Card (Soft Light Style) -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Buku Besar Keuangan Multi-Mata Uang</h3>
                <p class="text-[11px] text-slate-400">
                    Tagihan dasar: <span class="font-bold text-slate-700 font-mono">{{ $shipment->currency_code }}</span> • Tampilan aktif: <span class="font-bold text-blue-600 font-mono">{{ $activeCurrencyCode }}</span>
                </p>
            </div>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                1 USD = {{ $currencyService->getCurrency($activeCurrencyCode)?->exchange_rate_to_usd ?? 1 }} {{ $activeCurrencyCode }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Biaya Kirim (Freight)</span>
                <span class="font-bold text-slate-900 font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->shipping_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">Asli: {{ $currencyService->format($shipment->shipping_cost, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Nilai Kargo Dinyatakan</span>
                <span class="font-bold text-slate-900 font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->declared_value, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">Asli: {{ $currencyService->format($shipment->declared_value, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Premi Asuransi</span>
                <span class="font-bold text-slate-900 font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->insurance_fee, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-slate-400 font-mono mt-0.5">Asli: {{ $currencyService->format($shipment->insurance_fee, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-xl bg-blue-50/60 border border-blue-200">
                <span class="text-[10px] uppercase font-bold text-blue-700 block mb-0.5">Total Tagihan</span>
                <span class="font-extrabold text-blue-700 font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->total_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-slate-500 font-mono mt-0.5">Asli: {{ $currencyService->format($shipment->total_cost, $shipment->currency_code) }}</span>
            </div>
        </div>
    </div>

    <!-- Shopee / Modern Logistics Style Map & Tracking Container -->
    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-sm overflow-hidden space-y-0">
        <!-- Map Header Bar with Legend -->
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-sm shadow-2xs">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Rute Pengiriman & Posisi Terkini</h3>
                    <p class="text-[11px] text-slate-400">
                        Lokasi Terkini: <strong class="text-slate-700">{{ $shipment->current_location_name ?: 'En Route' }}</strong>
                    </p>
                </div>
            </div>

            <!-- Legend Pills -->
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-100"></span> Asal</span>
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-blue-600 ring-2 ring-blue-100"></span> Tujuan</span>
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-rose-100"></span> Posisi Kargo</span>
            </div>
        </div>

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
            <button type="button" id="recenterMapBtn" class="absolute bottom-4 right-4 z-[400] bg-white/95 backdrop-blur-xs w-11 h-11 rounded-full shadow-lg border border-slate-200/90 flex items-center justify-center text-slate-700 hover:text-rose-600 hover:scale-105 active:scale-95 transition" title="Pusatkan ke Posisi Kargo">
                <i class="fa-solid fa-crosshairs text-lg"></i>
            </button>

            <!-- Map Div -->
            <div id="shipmentDetailMap" class="w-full h-full"></div>
        </div>

        <!-- Integrated Shopee / SPX Delivery Status Card (Bottom of Map) -->
        <div class="p-5 sm:p-6 bg-white border-t border-slate-200/90 space-y-5">
            <!-- Estimasi Tiba Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-teal-600 block mb-0.5">
                        <i class="fa-regular fa-calendar-check mr-1"></i> Estimasi Pengiriman Global
                    </span>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-800">
                        Estimasi tiba: {{ $shipment->estimated_arrival->format('d M') }} – {{ $shipment->estimated_arrival->copy()->addDays(2)->format('d M Y') }}
                    </h2>
                </div>
                <div class="flex items-center gap-2">
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
                    <!-- Base gray bar -->
                    <div class="absolute left-10 right-10 top-5 h-0.5 bg-slate-200 -z-0"></div>
                    <!-- Active green fill bar -->
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

                    <!-- Step 2: Menuju Alamat / Pelabuhan Tujuan -->
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

            <!-- Garansi Tiba Badge (Shopee Style) -->
            <div class="p-3.5 rounded-xl bg-teal-50/80 border border-teal-200/90 text-xs text-teal-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-teal-500 text-white flex items-center justify-center text-xs flex-shrink-0">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <strong class="font-semibold text-teal-950">Garansi Tiba:</strong> Jaminan SLA ketepatan waktu pengiriman global CargoTrace.
                    </div>
                </div>
                <span class="text-[11px] font-bold text-teal-700 bg-white px-2.5 py-1 rounded-md border border-teal-200 shadow-2xs whitespace-nowrap self-start sm:self-auto">
                    Verified Service
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
                            <span class="font-mono text-xs font-bold text-slate-700" id="trackingNumSpan">{{ $shipment->shipment_number }}</span>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $shipment->shipment_number }}'); this.querySelector('span').innerText='Tersalin!'; setTimeout(() => this.querySelector('span').innerText='Salin', 2000);"
                                    class="text-[11px] text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1 transition">
                                <i class="fa-regular fa-copy"></i>
                                <span>Salin</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Rute: <strong class="text-slate-800">{{ $shipment->origin->city }} ({{ $shipment->origin->code }}) → {{ $shipment->destination->city }} ({{ $shipment->destination->code }})</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations & SLA Control Panel -->
    @can('update', $shipment)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Milestone Transition Form -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Kemajuan Status Siklus Hidup</h3>
            <p class="text-[11px] text-slate-400">Pindahkan tahapan status pengiriman. Status saat ini: <strong>{{ $shipment->current_status }}</strong></p>

            @if($shipment->isTerminal())
            <div class="p-3 rounded-xl bg-slate-50 text-xs text-slate-500 border border-slate-200">
                Konsinyasi berada dalam status akhir ({{ $shipment->current_status }}) dan tidak dapat diubah lagi.
            </div>
            @else
            <form action="{{ route('shipments.status', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Target Status Berikutnya</label>
                    <select name="status" required class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500">
                        @foreach(\App\Models\Shipment::VALID_TRANSITIONS[$shipment->current_status] ?? [] as $next)
                            <option value="{{ $next }}">{{ \App\Services\TranslationHelper::translateStatus($next) }} ({{ $next }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Catatan</label>
                    <input type="text" name="notes" placeholder="Rincian catatan perpindahan status..." class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-xs transition">
                    Perbarui Status
                </button>
            </form>
            @endif
        </div>

        <!-- Add Checkpoint Form -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Tambah Titik Pemeriksaan</h3>
            <p class="text-[11px] text-slate-400">Menambahkan titik koordinat telemetri beserta integrasi cuaca riil.</p>

            <form action="{{ route('shipments.tracking.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status</label>
                        <select name="status" required class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500">
                            <option value="IN_TRANSIT">Dalam Perjalanan (IN TRANSIT)</option>
                            <option value="PORT_ARRIVAL">Tiba di Pelabuhan (PORT ARRIVAL)</option>
                            <option value="CUSTOMS_CLEARANCE">Pemeriksaan Bea Cukai (CUSTOMS)</option>
                            <option value="DEPARTED">Berangkat (DEPARTED)</option>
                            <option value="DELIVERED">Terkirim (DELIVERED)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Lokasi</label>
                        <input type="text" name="location_name" required placeholder="Pelabuhan atau titik koordinat laut" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" placeholder="contoh: 1.2902" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" placeholder="contoh: 103.8519" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Deskripsi Catatan</label>
                    <input type="text" name="description" required placeholder="Catatan titik pemeriksaan..." class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:outline-none">
                </div>

                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs shadow-xs transition">
                    Simpan Titik Pemeriksaan
                </button>
            </form>
        </div>
    </div>
    @endcan

    <!-- Exceptions SLA Center -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Manajemen Insiden & Pengecualian SLA</h3>
                <p class="text-[11px] text-slate-400">Pemeriksaan kepabeanan, kendala cuaca badai, dan penanganan karantina</p>
            </div>

            @can('update', $shipment)
            <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.toggle('hidden')" class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-semibold transition">
                + Laporkan Insiden Baru
            </button>
            @endcan
        </div>

        <!-- Hidden Drawer to Raise Exception -->
        <div id="raiseExceptionDrawer" class="hidden p-4 rounded-xl bg-slate-50 border border-red-200 space-y-3">
            <h4 class="text-xs font-bold uppercase text-red-700">Catat Insiden Pengecualian Baru</h4>
            <form action="{{ route('shipments.exceptions.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis Insiden</label>
                        <select name="exception_type" required class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl">
                            <option value="CUSTOMS_HOLD">Penahanan Bea Cukai (CUSTOMS HOLD)</option>
                            <option value="WEATHER_DELAY">Kendala Cuaca / Badai (WEATHER DELAY)</option>
                            <option value="PORT_CONGESTION">Antrean Pelabuhan (PORT CONGESTION)</option>
                            <option value="DAMAGED">Kargo Rusak (DAMAGED CARGO)</option>
                            <option value="MISROUTED">Salah Rute (MISROUTED)</option>
                            <option value="DOCUMENTATION_ISSUE">Kendala Dokumen (DOCUMENTATION ISSUE)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tingkat Keparahan</label>
                        <select name="severity" required class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl">
                            <option value="LOW">Rendah / LOW (0 - 6 jam)</option>
                            <option value="MEDIUM" selected>Sedang / MEDIUM (6 - 24 jam)</option>
                            <option value="HIGH">Tinggi / HIGH (> 24 jam)</option>
                            <option value="CRITICAL">Kritis / CRITICAL</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dampak Keterlambatan (Jam)</label>
                        <input type="number" name="delay_impact_hours" min="0" value="12" class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Penyebab Masalah (Root Cause)</label>
                    <textarea name="root_cause" required rows="2" placeholder="Penjelasan penyebab kendala atau insiden..." class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.add('hidden')" class="px-3 py-1 text-xs text-slate-500">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-red-600 text-white font-semibold text-xs">Simpan Insiden</button>
                </div>
            </form>
        </div>

        <!-- Exceptions List -->
        <div class="space-y-2">
            @forelse($shipment->exceptions as $exc)
            <div class="p-3.5 rounded-xl {{ $exc->isOpen() ? 'bg-red-50/60 border border-red-200' : 'bg-slate-50 border border-slate-200' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900">{{ $exc->localized_type }}</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold {{ $exc->isOpen() ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                            {{ $exc->isOpen() ? 'DIPROSES' : 'SELESAI' }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-mono">{{ $exc->localized_severity }}</span>
                    </div>
                    <p class="text-slate-700 mt-1">{{ \App\Services\TranslationHelper::translateText($exc->root_cause) }}</p>
                    @if($exc->resolution_notes)
                    <p class="text-[11px] text-emerald-700 mt-1">Solusi Penyelesaian: {{ $exc->resolution_notes }}</p>
                    @endif
                </div>

                @if($exc->isOpen() && Auth::user()->canManageShipments())
                <form action="{{ route('exceptions.resolve', $exc->id) }}" method="POST" class="flex items-center gap-2 shrink-0">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="resolution_notes" required placeholder="Ringkasan penyelesaian..." class="px-2.5 py-1 text-xs bg-white border border-slate-200 rounded-lg">
                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 text-white font-semibold text-xs">
                        Selesaikan
                    </button>
                </form>
                @endif
            </div>
            @empty
            <p class="text-xs text-slate-400 text-center py-2">Tidak ada insiden atau kendala aktif untuk pengiriman ini.</p>
            @endforelse
        </div>
    </div>

    <!-- Timeline & Manifest -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Checkpoint Timeline -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm lg:col-span-2 space-y-4">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Garis Waktu Pelacakan</h3>

            <div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @forelse($shipment->trackingEvents as $ev)
                <div class="relative">
                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px]
                        {{ $ev->is_exception ? 'bg-red-500 text-white' : 'bg-blue-600 text-white' }}">
                        <i class="fa-solid fa-check text-[9px]"></i>
                    </div>

                    <div class="p-3.5 rounded-xl {{ $ev->is_exception ? 'bg-red-50/50 border border-red-200' : 'bg-slate-50 border border-slate-100' }}">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="text-xs font-bold text-slate-900">{{ $ev->localized_status }} • {{ $ev->localized_location_name }}</span>
                            <span class="text-[10px] font-mono text-slate-400">{{ $ev->event_time->format('d M, H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-600">{{ $ev->localized_description }}</p>
                        @if($ev->weather_summary)
                        <span class="inline-block mt-1 text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                            {{ $ev->localized_weather }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Belum ada titik pemeriksaan tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Manifest Items -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Item Manifest</h3>

            <div class="space-y-2">
                @forelse($shipment->items as $item)
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    <div class="font-bold text-slate-800">{{ $item->localized_description }}</div>
                    <div class="grid grid-cols-2 gap-1 mt-1 text-[11px] text-slate-500">
                        <div>Jml: <strong>{{ $item->quantity }}</strong></div>
                        <div>Berat: <strong>{{ number_format($item->weight_kg, 1) }} kg</strong></div>
                        <div>Kemasan: {{ $item->localized_package_type }}</div>
                        <div>Nilai: {{ $currencyService->format($item->declared_value, $shipment->currency_code) }}</div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">Kontainer kargo terkonsolidasi.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
        const mapData = {!! json_encode($mapPoints) !!};
        if (!mapData || mapData.length === 0) return;

        let currentPos = null;
        mapData.forEach(pt => {
            if (pt.type === 'current') currentPos = pt;
        });
        if (!currentPos) {
            currentPos = mapData[mapData.length - 1] || mapData[0];
        }

        // Initialize Map
        const map = L.map('shipmentDetailMap', {
            center: [currentPos.lat, currentPos.lng],
            zoom: 4,
            minZoom: 2,
            maxZoom: 18,
            worldCopyJump: true
        });

        // OpenStreetMap Standard Tiles (No API Key Required, Natural Green Land & Blue Sea)
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        const latLngs = [];
        let vehicleMarker = null;

        // Determine Shopee Callout Status Text
        const shipmentStatus = "{{ $shipment->current_status }}";
        let calloutStatus = "Memproses pesanan";
        if (shipmentStatus === 'IN_TRANSIT') {
            calloutStatus = "Sedang Berlayar";
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
                // Shopee-style Custom Vehicle Marker with Speech Bubble
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

                let popupContent = `
                    <div style="font-family: inherit; font-size: 12px; padding: 2px;">
                        <strong style="color: #0f172a; font-size: 13px;">${pt.title}</strong><br>
                        <span style="color: #64748b;">${pt.location || ''}</span>
                        ${pt.weather ? `<br><span style="color: #0284c7; font-weight: 600; font-size: 11px;">☀️ Cuaca: ${pt.weather}</span>` : ''}
                    </div>
                `;
                vehicleMarker.bindPopup(popupContent);

            } else if (pt.type === 'origin') {
                const originIcon = L.divIcon({
                    className: 'custom-origin-div',
                    html: `<div class="port-pin bg-emerald-600"><i class="fa-solid fa-anchor"></i></div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
                const m = L.marker([pt.lat, pt.lng], { icon: originIcon }).addTo(map);
                m.bindPopup(`<b>Pelabuhan Asal:</b> ${pt.location}<br><span style="font-size:11px;color:#64748b;">Waktu: ${pt.time || '-'}</span>`);

            } else if (pt.type === 'destination') {
                const destIcon = L.divIcon({
                    className: 'custom-dest-div',
                    html: `<div class="port-pin bg-blue-600"><i class="fa-solid fa-location-dot"></i></div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
                const m = L.marker([pt.lat, pt.lng], { icon: destIcon }).addTo(map);
                m.bindPopup(`<b>Pelabuhan Tujuan:</b> ${pt.location}`);

            } else {
                // Waypoint / Exception Checkpoint
                const isExc = pt.type === 'exception';
                const wpIcon = L.circleMarker([pt.lat, pt.lng], {
                    radius: isExc ? 7 : 5,
                    fillColor: isExc ? '#ef4444' : '#3b82f6',
                    color: '#ffffff',
                    weight: 2,
                    fillOpacity: 0.9
                }).addTo(map);

                let popupContent = `<b>${pt.title}</b><br>${pt.location}`;
                if (pt.time) popupContent += `<br><span style="color:#64748b;font-size:11px;">Waktu: ${pt.time}</span>`;
                if (pt.weather) popupContent += `<br><span style="color:#0284c7;font-size:11px;">Cuaca: ${pt.weather}</span>`;
                wpIcon.bindPopup(popupContent);
            }
        });

        // Shopee / Logistics Style Polyline (Vibrant Coral/Orange with Route Outline)
        if (latLngs.length > 1) {
            // Background glow line
            L.polyline(latLngs, {
                color: '#ea580c',
                weight: 5,
                opacity: 0.85
            }).addTo(map);

            // Dashed inner navigational track
            L.polyline(latLngs, {
                color: '#ffffff',
                weight: 2,
                opacity: 0.9,
                dashArray: '6, 8'
            }).addTo(map);

            map.fitBounds(L.polyline(latLngs).getBounds(), { padding: [50, 50] });
        }

        // GPS Re-Center Button Click (Shopee Style)
        const recenterBtn = document.getElementById('recenterMapBtn');
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
    });
</script>
@endpush
