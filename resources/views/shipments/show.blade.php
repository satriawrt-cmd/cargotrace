@extends('layouts.app')

@section('title', 'Pengiriman ' . $shipment->shipment_number)

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.index') }}" class="p-2 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#1E3A8A] border border-[#E2E8F0] shadow-2xs transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Konsinyasi</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-[#38BDF8]/70 text-[#1E3A8A] font-mono font-bold border border-[#7DD3FC]">TERVERIFIKASI</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#0F172A] font-mono mt-0.5">{{ $shipment->shipment_number }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('update', $shipment)
            <a href="{{ route('shipments.edit', $shipment->id) }}" class="px-3 py-1.5 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#1E3A8A] border border-[#E2E8F0] text-xs font-semibold shadow-2xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square text-[#64748B] text-[11px]"></i>
                <span>Edit</span>
            </a>
            @endcan

            @can('delete', $shipment)
            <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" onsubmit="return confirm('Hapus permanen pengiriman ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#FEF2F2] hover:bg-[#FCE7E4] text-[#DC2626] text-xs font-semibold transition border border-[#FECACA]">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Main Overview Card (Ocean Maritime Style) -->
    <div class="p-6 sm:p-7 rounded-3xl bg-white/95 border border-[#E2E8F0] shadow-sm space-y-6">
        <!-- Route Banner & Status -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-[#E2E8F0]">
            <!-- Origin -> Destination -->
            <div class="flex items-center gap-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Asal</span>
                    <h3 class="text-base sm:text-lg font-bold text-[#0F172A]">{{ $shipment->origin->city }}</h3>
                    <p class="text-xs text-[#64748B]">{{ $shipment->origin->country }} ({{ $shipment->origin->code }})</p>
                </div>

                <div class="flex flex-col items-center px-2">
                    <span class="text-[10px] text-[#64748B] mb-0.5">{{ $shipment->carrier->service_type }}</span>
                    <div class="w-20 sm:w-28 flex items-center">
                        <div class="h-0.5 bg-[#E2E8F0] flex-1"></div>
                        <i class="fa-solid fa-ship text-[#1E3A8A] text-xs mx-2"></i>
                        <div class="h-0.5 bg-[#E2E8F0] flex-1"></div>
                    </div>
                    <span class="text-[11px] font-semibold text-[#475569] mt-0.5">{{ $shipment->carrier->name }}</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#64748B]">Tujuan</span>
                    <h3 class="text-base sm:text-lg font-bold text-[#0F172A]">{{ $shipment->destination->city }}</h3>
                    <p class="text-xs text-[#64748B]">{{ $shipment->destination->country }} ({{ $shipment->destination->code }})</p>
                </div>
            </div>

            <!-- Status & Delay Badge -->
            <div class="flex items-center gap-2">
                @if($shipment->current_status === 'DELIVERED')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0]">
                        Terkirim
                    </span>
                @elseif($shipment->current_status === 'IN_TRANSIT')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#E0F2FE] text-[#1E3A8A] border border-[#E2D2C0]">
                        Dalam Perjalanan
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF4ED] text-[#475569] border border-[#E2E8F0]">
                        {{ $shipment->current_status === 'CREATED' ? 'Dibuat' : ($shipment->current_status === 'BOOKED' ? 'Terjadwal' : ($shipment->current_status === 'PICKED_UP' ? 'Diambil' : str_replace('_', ' ', $shipment->current_status))) }}
                    </span>
                @endif

                @if($shipment->delay_status === 'DELAYED')
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FFF5EC] text-[#D97706] border border-[#F2DAC4]">
                        Terlambat (+{{ $shipment->delay_hours }}j)
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0]">
                        Tepat Waktu
                    </span>
                @endif
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                <span class="text-[#64748B] font-semibold uppercase text-[10px] block mb-1">Jadwal Keberangkatan</span>
                <span class="font-bold text-[#0F172A] font-mono">{{ $shipment->expected_departure->format('d M Y H:i') }}</span>
                @if($shipment->actual_departure)
                <p class="text-[10px] text-[#047857] mt-1">Aktual: {{ $shipment->actual_departure->format('d M, H:i') }}</p>
                @endif
            </div>

            <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                <span class="text-[#64748B] font-semibold uppercase text-[10px] block mb-1">Estimasi Tiba (ETA)</span>
                <span class="font-bold text-[#0F172A] font-mono">{{ $shipment->estimated_arrival->format('d M Y H:i') }}</span>
                <p class="text-[10px] text-[#64748B] mt-1">Diharapkan: {{ $shipment->expected_arrival->format('d M, H:i') }}</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                <span class="text-[#64748B] font-semibold uppercase text-[10px] block mb-1">Kargo & Berat</span>
                <span class="font-bold text-[#0F172A] line-clamp-1">{{ $shipment->localized_cargo_type }}</span>
                <p class="text-[10px] text-[#64748B] mt-1">{{ number_format($shipment->weight_kg, 1) }} kg • {{ number_format($shipment->volume_cbm, 1) }} CBM</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                <span class="text-[#64748B] font-semibold uppercase text-[10px] block mb-1">Penerima (Consignee)</span>
                <span class="font-bold text-[#0F172A]">{{ $shipment->customer->name }}</span>
                <p class="text-[10px] text-[#64748B] mt-1">{{ $shipment->customer->company_name ?? $shipment->customer->email }}</p>
            </div>
        </div>

        @if($shipment->delay_reason)
        <div class="p-3 rounded-2xl bg-[#FFF6ED] border border-[#F3DAC5] text-xs text-[#D97706] flex items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation text-[#D97706] mt-0.5"></i>
            <div>
                <strong>Pemberitahuan Keterlambatan:</strong> {{ $shipment->delay_reason }}
            </div>
        </div>
        @endif
    </div>

    <!-- Multi-Currency Financial Ledger Card (Ocean Maritime Style) -->
    <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#E2E8F0] shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-[#0F172A]">Buku Besar Keuangan Multi-Mata Uang</h3>
                <p class="text-[11px] text-[#64748B]">
                    Tagihan dasar: <span class="font-bold text-[#0F172A] font-mono">{{ $shipment->currency_code }}</span> • Tampilan aktif: <span class="font-bold text-[#1E3A8A] font-mono">{{ $activeCurrencyCode }}</span>
                </p>
            </div>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-[#F8FAFC] text-[#1E3A8A] border border-[#E2E8F0]">
                1 USD = {{ $currencyService->getCurrency($activeCurrencyCode)?->exchange_rate_to_usd ?? 1 }} {{ $activeCurrencyCode }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            <div class="p-3 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                <span class="text-[10px] uppercase font-bold text-[#64748B] block mb-0.5">Biaya Kirim (Freight)</span>
                <span class="font-bold text-[#0F172A] font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->shipping_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-[#64748B] font-mono mt-0.5">Asli: {{ $currencyService->format($shipment->shipping_cost, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                <span class="text-[10px] uppercase font-bold text-[#64748B] block mb-0.5">Nilai Kargo Dinyatakan</span>
                <span class="font-bold text-[#0F172A] font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->declared_value, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-[#64748B] font-mono mt-0.5">Asli: {{ $currencyService->format($shipment->declared_value, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                <span class="text-[10px] uppercase font-bold text-[#64748B] block mb-0.5">Premi Asuransi</span>
                <span class="font-bold text-[#0F172A] font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->insurance_fee, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-[#64748B] font-mono mt-0.5">Asli: {{ $currencyService->format($shipment->insurance_fee, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-[#38BDF8]/40 border border-[#7DD3FC]">
                <span class="text-[10px] uppercase font-bold text-[#1E3A8A] block mb-0.5">Total Tagihan</span>
                <span class="font-extrabold text-[#1E3A8A] font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->total_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-[#8C7664] font-mono mt-0.5">Asli: {{ $currencyService->format($shipment->total_cost, $shipment->currency_code) }}</span>
            </div>
        </div>
    </div>

    <!-- Map & Tracking Container (Ocean Maritime Style) -->
    <div class="rounded-3xl bg-white border border-[#E2E8F0] shadow-sm overflow-hidden space-y-0">
        <!-- Map Header Bar with Legend -->
        <div class="px-5 py-3.5 bg-[#F8FAFC]/90 border-b border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#38BDF8] text-[#1E3A8A] border border-[#7DD3FC] flex items-center justify-center text-sm shadow-2xs">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <div>
                    <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider">Rute Pengiriman & Posisi Terkini</h3>
                    <p class="text-[11px] text-[#64748B]">
                        Lokasi Terkini: <strong class="text-[#0F172A]">{{ $shipment->current_location_name ?: 'En Route' }}</strong>
                    </p>
                </div>
            </div>

            <!-- Legend Pills -->
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-[#475569]"><span class="w-2.5 h-2.5 rounded-full bg-[#1E3A8A] ring-2 ring-[#38BDF8]"></span> Asal</span>
                <span class="flex items-center gap-1.5 text-[#475569]"><span class="w-2.5 h-2.5 rounded-full bg-[#64748B] ring-2 ring-[#94A3B8]"></span> Tujuan</span>
                <span class="flex items-center gap-1.5 text-[#475569]"><span class="w-2.5 h-2.5 rounded-full bg-[#D97706] ring-2 ring-[#38BDF8]"></span> Posisi Kargo</span>
            </div>
        </div>

        <!-- Relative Map Frame with Compass & GPS Locate Button Overlay -->
        <div class="relative w-full h-[440px] z-0">
            <!-- Compass North Overlay -->
            <div class="absolute top-3 left-3 z-[400] bg-white/95 backdrop-blur-xs p-1.5 rounded-full shadow-md border border-[#E2E8F0] flex items-center justify-center w-10 h-10 select-none hover:bg-[#F8FAFC] transition cursor-pointer" title="Arah Utara (North)">
                <div class="relative w-6 h-6 flex items-center justify-center">
                    <div class="w-1 h-3 bg-[#D97706] rounded-t-sm absolute top-0"></div>
                    <div class="w-1 h-3 bg-[#94A3B8] rounded-b-sm absolute bottom-0"></div>
                    <span class="text-[8px] font-black text-[#D97706] absolute -top-1">U</span>
                </div>
            </div>

            <!-- GPS Re-Center Target Button -->
            <button type="button" id="recenterMapBtn" class="absolute bottom-4 right-4 z-[400] bg-white/95 backdrop-blur-xs w-11 h-11 rounded-full shadow-lg border border-[#E2E8F0] flex items-center justify-center text-[#1E3A8A] hover:text-[#0F172A] hover:scale-105 active:scale-95 transition" title="Pusatkan ke Posisi Kargo">
                <i class="fa-solid fa-crosshairs text-lg"></i>
            </button>

            <!-- Map Div -->
            <div id="shipmentDetailMap" class="w-full h-full"></div>
        </div>

        <!-- Integrated Delivery Status Card (Bottom of Map) -->
        <div class="p-5 sm:p-6 bg-white border-t border-[#E2E8F0] space-y-5">
            <!-- Estimasi Tiba Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-[#E2E8F0]">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#64748B] block mb-0.5">
                        <i class="fa-regular fa-calendar-check mr-1"></i> Estimasi Pengiriman Global
                    </span>
                    <h2 class="text-lg sm:text-xl font-extrabold text-[#0F172A]">
                        Estimasi tiba: {{ $shipment->estimated_arrival->format('d M') }} – {{ $shipment->estimated_arrival->copy()->addDays(2)->format('d M Y') }}
                    </h2>
                </div>
                <div class="flex items-center gap-2">
                    @if($shipment->delay_status === 'DELAYED')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FFF5EC] text-[#D97706] border border-[#F2DAC4] flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#D97706]"></span>
                            Terlambat (+{{ $shipment->delay_hours }} jam)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#ECFDF5] text-[#047857] border border-[#A7F3D0] flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#047857]"></span>
                            Tepat Waktu (On Schedule)
                        </span>
                    @endif
                </div>
            </div>

            <!-- 3-Step Milestone Stepper -->
            <div class="py-2">
                <div class="relative flex items-center justify-between max-w-2xl mx-auto px-4 sm:px-10">
                    <div class="absolute left-10 right-10 top-5 h-0.5 bg-[#E2E8F0] -z-0"></div>
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
                    <div class="absolute left-10 top-5 h-0.5 bg-[#1E3A8A] -z-0 transition-all duration-500" style="width: {{ $stepperPct }};"></div>

                    <!-- Step 1: Sedang Dikirim -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ in_array($shipment->current_status, ['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED']) ? 'bg-[#1E3A8A] text-[#FFF8F2] ring-4 ring-[#38BDF8]' : 'bg-[#F8FAFC] text-[#64748B]' }}">
                            <i class="fa-solid fa-box-archive"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ in_array($shipment->current_status, ['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED']) ? 'text-[#0F172A]' : 'text-[#64748B]' }}">
                            Sedang Dikirim
                        </span>
                        <span class="text-[10px] text-[#64748B] max-w-[90px] truncate">{{ $shipment->origin->city }}</span>
                    </div>

                    <!-- Step 2: Menuju Alamat / Pelabuhan Tujuan -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ in_array($shipment->current_status, ['IN_TRANSIT', 'DELIVERED']) ? 'bg-[#1E3A8A] text-[#FFF8F2] ring-4 ring-[#38BDF8]' : 'bg-[#F8FAFC] text-[#64748B]' }}">
                            <i class="fa-solid fa-ship"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ in_array($shipment->current_status, ['IN_TRANSIT', 'DELIVERED']) ? 'text-[#0F172A]' : 'text-[#64748B]' }}">
                            Menuju Tujuan
                        </span>
                        <span class="text-[10px] text-[#64748B]">Dalam Pelayaran</span>
                    </div>

                    <!-- Step 3: Pesanan Tiba -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ $shipment->current_status === 'DELIVERED' ? 'bg-[#1E3A8A] text-[#FFF8F2] ring-4 ring-[#38BDF8]' : 'bg-[#F8FAFC] text-[#64748B]' }}">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ $shipment->current_status === 'DELIVERED' ? 'text-[#0F172A]' : 'text-[#64748B]' }}">
                            Pesanan Tiba
                        </span>
                        <span class="text-[10px] text-[#64748B] max-w-[90px] truncate">{{ $shipment->destination->city }}</span>
                    </div>
                </div>
            </div>

            <!-- Garansi Tiba Badge -->
            <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] text-xs text-[#1D4ED8] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-[#38BDF8] text-[#1E3A8A] flex items-center justify-center text-xs flex-shrink-0 shadow-2xs">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <strong class="font-semibold text-[#0F172A]">Garansi Tiba:</strong> Jaminan SLA ketepatan waktu pengiriman global CargoTrace.
                    </div>
                </div>
                <span class="text-[11px] font-bold text-[#1E3A8A] bg-[#38BDF8]/70 px-2.5 py-1 rounded-md border border-[#7DD3FC] shadow-2xs whitespace-nowrap self-start sm:self-auto">
                    Verified Service
                </span>
            </div>

            <!-- Kurir & Resi Card with 1-Click Copy -->
            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white border border-[#E2E8F0] shadow-2xs flex items-center justify-center text-[#1E3A8A] text-xl">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#0F172A]">{{ $shipment->carrier->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#38BDF8]/50 text-[#1E3A8A] font-semibold uppercase">{{ $shipment->carrier->service_type }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-mono text-xs font-bold text-[#1D4ED8]" id="trackingNumSpan">{{ $shipment->shipment_number }}</span>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $shipment->shipment_number }}'); this.querySelector('span').innerText='Tersalin!'; setTimeout(() => this.querySelector('span').innerText='Salin', 2000);"
                                    class="text-[11px] text-[#1E3A8A] hover:text-[#0F172A] font-semibold flex items-center gap-1 transition">
                                <i class="fa-regular fa-copy"></i>
                                <span>Salin</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#64748B]">Rute: <strong class="text-[#0F172A]">{{ $shipment->origin->city }} ({{ $shipment->origin->code }}) → {{ $shipment->destination->city }} ({{ $shipment->destination->code }})</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations & SLA Control Panel -->
    @can('update', $shipment)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Milestone Transition Form -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#E2E8F0] shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider">Kemajuan Status Siklus Hidup</h3>
            <p class="text-[11px] text-[#64748B]">Pindahkan tahapan status pengiriman. Status saat ini: <strong>{{ $shipment->current_status }}</strong></p>

            @if($shipment->isTerminal())
            <div class="p-3 rounded-2xl bg-[#F8FAFC] text-xs text-[#64748B] border border-[#E2E8F0]">
                Konsinyasi berada dalam status akhir ({{ $shipment->current_status }}) dan tidak dapat diubah lagi.
            </div>
            @else
            <form action="{{ route('shipments.status', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-[#475569] mb-1">Target Status Berikutnya</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] focus:outline-none focus:border-[#64748B]">
                        @foreach(\App\Models\Shipment::VALID_TRANSITIONS[$shipment->current_status] ?? [] as $next)
                            <option value="{{ $next }}">{{ \App\Services\TranslationHelper::translateStatus($next) }} ({{ $next }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-[#475569] mb-1">Catatan</label>
                    <input type="text" name="notes" placeholder="Rincian catatan perpindahan status..." class="w-full px-3 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] focus:outline-none focus:border-[#64748B]">
                </div>
                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-[#1E3A8A] hover:bg-[#1D4ED8] text-[#FFF8F2] font-semibold text-xs shadow-sm transition border border-[#1D4ED8]">
                    Perbarui Status
                </button>
            </form>
            @endif
        </div>

        <!-- Add Checkpoint Form -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#E2E8F0] shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider">Tambah Titik Pemeriksaan</h3>
            <p class="text-[11px] text-[#64748B]">Menambahkan titik koordinat telemetri beserta integrasi cuaca riil.</p>

            <form action="{{ route('shipments.tracking.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] mb-1">Status</label>
                        <select name="status" required class="w-full px-3 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] focus:outline-none focus:border-[#64748B]">
                            <option value="IN_TRANSIT">Dalam Perjalanan (IN TRANSIT)</option>
                            <option value="PORT_ARRIVAL">Tiba di Pelabuhan (PORT ARRIVAL)</option>
                            <option value="CUSTOMS_CLEARANCE">Pemeriksaan Bea Cukai (CUSTOMS)</option>
                            <option value="DEPARTED">Berangkat (DEPARTED)</option>
                            <option value="DELIVERED">Terkirim (DELIVERED)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] mb-1">Nama Lokasi</label>
                        <input type="text" name="location_name" required placeholder="Pelabuhan atau titik koordinat laut" class="w-full px-3 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] focus:outline-none focus:border-[#64748B]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" placeholder="contoh: 1.2902" class="w-full px-3 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" placeholder="contoh: 103.8519" class="w-full px-3 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#475569] mb-1">Deskripsi Catatan</label>
                    <input type="text" name="description" required placeholder="Catatan titik pemeriksaan..." class="w-full px-3 py-2 text-xs bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[#0F172A] focus:outline-none">
                </div>

                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-[#1D4ED8] hover:bg-[#0F172A] text-[#FFF8F2] font-semibold text-xs shadow-sm transition">
                    Simpan Titik Pemeriksaan
                </button>
            </form>
        </div>
    </div>
    @endcan

    <!-- Exceptions SLA Center -->
    <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#E2E8F0] shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#0F172A]">Manajemen Insiden & Pengecualian SLA</h3>
                <p class="text-[11px] text-[#64748B]">Pemeriksaan kepabeanan, kendala cuaca badai, dan penanganan karantina</p>
            </div>

            @can('update', $shipment)
            <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.toggle('hidden')" class="px-3 py-1.5 rounded-xl bg-[#FFF5EC] hover:bg-[#FDEBD9] text-[#D97706] border border-[#F2DAC4] text-xs font-semibold transition">
                + Laporkan Insiden Baru
            </button>
            @endcan
        </div>

        <!-- Hidden Drawer to Raise Exception -->
        <div id="raiseExceptionDrawer" class="hidden p-4 rounded-2xl bg-[#FFF6ED] border border-[#F2DAC4] space-y-3">
            <h4 class="text-xs font-bold uppercase text-[#D97706]">Catat Insiden Pengecualian Baru</h4>
            <form action="{{ route('shipments.exceptions.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] mb-1">Jenis Insiden</label>
                        <select name="exception_type" required class="w-full px-3 py-1.5 text-xs bg-white border border-[#E2E8F0] rounded-xl">
                            <option value="CUSTOMS_HOLD">Penahanan Bea Cukai (CUSTOMS HOLD)</option>
                            <option value="WEATHER_DELAY">Kendala Cuaca / Badai (WEATHER DELAY)</option>
                            <option value="PORT_CONGESTION">Antrean Pelabuhan (PORT CONGESTION)</option>
                            <option value="DAMAGED">Kargo Rusak (DAMAGED CARGO)</option>
                            <option value="MISROUTED">Salah Rute (MISROUTED)</option>
                            <option value="DOCUMENTATION_ISSUE">Kendala Dokumen (DOCUMENTATION ISSUE)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] mb-1">Tingkat Keparahan</label>
                        <select name="severity" required class="w-full px-3 py-1.5 text-xs bg-white border border-[#E2E8F0] rounded-xl">
                            <option value="LOW">Rendah / LOW (0 - 6 jam)</option>
                            <option value="MEDIUM" selected>Sedang / MEDIUM (6 - 24 jam)</option>
                            <option value="HIGH">Tinggi / HIGH (> 24 jam)</option>
                            <option value="CRITICAL">Kritis / CRITICAL</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] mb-1">Dampak Keterlambatan (Jam)</label>
                        <input type="number" name="delay_impact_hours" min="0" value="12" class="w-full px-3 py-1.5 text-xs bg-white border border-[#E2E8F0] rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#475569] mb-1">Penyebab Masalah (Root Cause)</label>
                    <textarea name="root_cause" required rows="2" placeholder="Penjelasan penyebab kendala atau insiden..." class="w-full px-3 py-1.5 text-xs bg-white border border-[#E2E8F0] rounded-xl"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.add('hidden')" class="px-3 py-1 text-xs text-[#64748B]">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-[#D97706] text-white font-semibold text-xs">Simpan Insiden</button>
                </div>
            </form>
        </div>

        <!-- Exceptions List -->
        <div class="space-y-2">
            @forelse($shipment->exceptions as $exc)
            <div class="p-3.5 rounded-2xl {{ $exc->isOpen() ? 'bg-[#FFF5EC] border border-[#F2DAC4]' : 'bg-[#F8FAFC] border border-[#E2E8F0]' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-[#0F172A]">{{ $exc->localized_type }}</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold {{ $exc->isOpen() ? 'bg-[#FDE7E3] text-[#963E32]' : 'bg-[#ECFDF5] text-[#047857]' }}">
                            {{ $exc->isOpen() ? 'DIPROSES' : 'SELESAI' }}
                        </span>
                        <span class="text-[10px] text-[#64748B] font-mono">{{ $exc->localized_severity }}</span>
                    </div>
                    <p class="text-[#475569] mt-1">{{ \App\Services\TranslationHelper::translateText($exc->root_cause) }}</p>
                    @if($exc->resolution_notes)
                    <p class="text-[11px] text-[#047857] mt-1">Solusi Penyelesaian: {{ $exc->resolution_notes }}</p>
                    @endif
                </div>

                @if($exc->isOpen() && Auth::user()->canManageShipments())
                <form action="{{ route('exceptions.resolve', $exc->id) }}" method="POST" class="flex items-center gap-2 shrink-0">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="resolution_notes" required placeholder="Ringkasan penyelesaian..." class="px-2.5 py-1 text-xs bg-white border border-[#E2E8F0] rounded-lg">
                    <button type="submit" class="px-3 py-1 rounded-lg bg-[#047857] text-white font-semibold text-xs">
                        Selesaikan
                    </button>
                </form>
                @endif
            </div>
            @empty
            <p class="text-xs text-[#64748B] text-center py-2">Tidak ada insiden atau kendala aktif untuk pengiriman ini.</p>
            @endforelse
        </div>
    </div>

    <!-- Timeline & Manifest -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Checkpoint Timeline -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#E2E8F0] shadow-sm lg:col-span-2 space-y-4">
            <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider">Garis Waktu Pelacakan</h3>

            <div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-[#E2E8F0]">
                @forelse($shipment->trackingEvents as $ev)
                <div class="relative">
                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px]
                        {{ $ev->is_exception ? 'bg-[#D97706] text-white' : 'bg-[#1E3A8A] text-[#FFF8F2]' }}">
                        <i class="fa-solid fa-check text-[9px]"></i>
                    </div>

                    <div class="p-3.5 rounded-2xl {{ $ev->is_exception ? 'bg-[#FFF5EC] border border-[#F2DAC4]' : 'bg-[#F8FAFC] border border-[#E2E8F0]' }}">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="text-xs font-bold text-[#0F172A]">{{ $ev->localized_status }} • {{ $ev->localized_location_name }}</span>
                            <span class="text-[10px] font-mono text-[#64748B]">{{ $ev->event_time->format('d M, H:i') }}</span>
                        </div>
                        <p class="text-xs text-[#475569]">{{ $ev->localized_description }}</p>
                        @if($ev->weather_summary)
                        <span class="inline-block mt-1 text-[10px] text-[#1E3A8A] bg-[#38BDF8]/50 px-2 py-0.5 rounded border border-[#7DD3FC]">
                            {{ $ev->localized_weather }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-[#64748B]">Belum ada titik pemeriksaan tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Manifest Items -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#E2E8F0] shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider">Daftar Item Manifest</h3>

            <div class="space-y-2">
                @forelse($shipment->items as $item)
                <div class="p-3 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] text-xs">
                    <div class="font-bold text-[#0F172A]">{{ $item->localized_description }}</div>
                    <div class="grid grid-cols-2 gap-1 mt-1 text-[11px] text-[#64748B]">
                        <div>Jml: <strong>{{ $item->quantity }}</strong></div>
                        <div>Berat: <strong>{{ number_format($item->weight_kg, 1) }} kg</strong></div>
                        <div>Kemasan: {{ $item->localized_package_type }}</div>
                        <div>Nilai: {{ $currencyService->format($item->declared_value, $shipment->currency_code) }}</div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-[#64748B]">Kontainer kargo terkonsolidasi.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
    .shopee-bubble {
        position: relative;
        background: #ffffff;
        color: #1E3A8A;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(102, 73, 48, 0.15);
        border: 1px solid #94A3B8;
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
        background: linear-gradient(135deg, #1E3A8A 0%, #0F172A 100%);
        border: 2.5px solid #38BDF8;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #38BDF8;
        font-size: 18px;
        box-shadow: 0 8px 18px rgba(102, 73, 48, 0.35);
        transition: transform 0.2s ease;
    }
    .vehicle-badge:hover {
        transform: scale(1.1);
    }
    .vehicle-radar {
        position: absolute;
        inset: -4px;
        border-radius: 16px;
        background: rgba(255, 219, 187, 0.45);
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
        box-shadow: 0 4px 10px rgba(102, 73, 48, 0.25);
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

        // OpenStreetMap Standard Tiles
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        const latLngs = [];
        let vehicleMarker = null;

        // Determine Callout Status Text
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
                const vehicleHtml = `
                    <div class="vehicle-callout-wrapper">
                        <div class="shopee-bubble">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#D97706] inline-block animate-ping"></span>
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
                        <strong style="color: #0F172A; font-size: 13px;">${pt.title}</strong><br>
                        <span style="color: #64748B;">${pt.location || ''}</span>
                        ${pt.weather ? `<br><span style="color: #1E3A8A; font-weight: 600; font-size: 11px;">☀️ Cuaca: ${pt.weather}</span>` : ''}
                    </div>
                `;
                vehicleMarker.bindPopup(popupContent);

            } else if (pt.type === 'origin') {
                const originIcon = L.divIcon({
                    className: 'custom-origin-div',
                    html: `<div class="port-pin bg-[#1E3A8A]"><i class="fa-solid fa-anchor"></i></div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
                const m = L.marker([pt.lat, pt.lng], { icon: originIcon }).addTo(map);
                m.bindPopup(`<b>Pelabuhan Asal:</b> ${pt.location}<br><span style="font-size:11px;color:#64748B;">Waktu: ${pt.time || '-'}</span>`);

            } else if (pt.type === 'destination') {
                const destIcon = L.divIcon({
                    className: 'custom-dest-div',
                    html: `<div class="port-pin bg-[#64748B]"><i class="fa-solid fa-location-dot"></i></div>`,
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
                    fillColor: isExc ? '#D97706' : '#64748B',
                    color: '#ffffff',
                    weight: 2,
                    fillOpacity: 0.95
                }).addTo(map);

                let popupContent = `<b>${pt.title}</b><br>${pt.location}`;
                if (pt.time) popupContent += `<br><span style="color:#64748B;font-size:11px;">Waktu: ${pt.time}</span>`;
                if (pt.weather) popupContent += `<br><span style="color:#1E3A8A;font-size:11px;">Cuaca: ${pt.weather}</span>`;
                wpIcon.bindPopup(popupContent);
            }
        });

        // Ocean Maritime Polyline (Warm Taupe with Soft Peach Center)
        if (latLngs.length > 1) {
            L.polyline(latLngs, {
                color: '#1E3A8A',
                weight: 5,
                opacity: 0.85
            }).addTo(map);

            L.polyline(latLngs, {
                color: '#38BDF8',
                weight: 2,
                opacity: 0.95,
                dashArray: '6, 8'
            }).addTo(map);

            map.fitBounds(L.polyline(latLngs).getBounds(), { padding: [50, 50] });
        }

        // GPS Re-Center Button Click
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