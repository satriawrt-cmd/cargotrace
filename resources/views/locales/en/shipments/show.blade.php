@extends('layouts.app')

@section('title', 'Shipment ' . $shipment->shipment_number)

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('shipments.index') }}" class="p-2 rounded-xl bg-white hover:bg-[#FAF5EE] text-[#664930] border border-[#EDE4DC] shadow-2xs transition">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#997E67]">Consignment</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-[#FFDBBB]/70 text-[#664930] font-mono font-bold border border-[#F0CCAC]">VERIFIED</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#3D2A1A] font-mono mt-0.5">{{ $shipment->shipment_number }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('update', $shipment)
            <a href="{{ route('shipments.edit', $shipment->id) }}" class="px-3 py-1.5 rounded-xl bg-white hover:bg-[#FAF5EE] text-[#664930] border border-[#EDE4DC] text-xs font-semibold shadow-2xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square text-[#997E67] text-[11px]"></i>
                <span>Edit</span>
            </a>
            @endcan

            @can('delete', $shipment)
            <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" onsubmit="return confirm('Permanently delete this shipment?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#FDF2F0] hover:bg-[#FCE7E4] text-[#8C3A30] text-xs font-semibold transition border border-[#F5D2CC]">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- Main Overview Card (Neutral Elegance Style) -->
    <div class="p-6 sm:p-7 rounded-3xl bg-white/95 border border-[#EDE4DC] shadow-sm space-y-6">
        <!-- Route Banner & Status -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-[#EDE4DC]">
            <!-- Origin -> Destination -->
            <div class="flex items-center gap-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#997E67]">Origin</span>
                    <h3 class="text-base sm:text-lg font-bold text-[#3D2A1A]">{{ $shipment->origin->city }}</h3>
                    <p class="text-xs text-[#7A6350]">{{ $shipment->origin->country }} ({{ $shipment->origin->code }})</p>
                </div>

                <div class="flex flex-col items-center px-2">
                    <span class="text-[10px] text-[#997E67] mb-0.5">{{ $shipment->carrier->service_type }}</span>
                    <div class="w-20 sm:w-28 flex items-center">
                        <div class="h-0.5 bg-[#EDE4DC] flex-1"></div>
                        <i class="fa-solid fa-ship text-[#664930] text-xs mx-2"></i>
                        <div class="h-0.5 bg-[#EDE4DC] flex-1"></div>
                    </div>
                    <span class="text-[11px] font-semibold text-[#6E5947] mt-0.5">{{ $shipment->carrier->name }}</span>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#997E67]">Destination</span>
                    <h3 class="text-base sm:text-lg font-bold text-[#3D2A1A]">{{ $shipment->destination->city }}</h3>
                    <p class="text-xs text-[#7A6350]">{{ $shipment->destination->country }} ({{ $shipment->destination->code }})</p>
                </div>
            </div>

            <!-- Status & Delay Badge -->
            <div class="flex items-center gap-2">
                @if($shipment->current_status === 'DELIVERED')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#EDF5EC] text-[#345937] border border-[#CCE2CB]">
                        Delivered
                    </span>
                @elseif($shipment->current_status === 'IN_TRANSIT')
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#F5EDE3] text-[#72543B] border border-[#E2D2C0]">
                        In Transit
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF4ED] text-[#6E5947] border border-[#E8DCCF]">
                        {{ str_replace('_', ' ', $shipment->current_status) }}
                    </span>
                @endif

                @if($shipment->delay_status === 'DELAYED')
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#FFF5EC] text-[#8C4626] border border-[#F2DAC4]">
                        Delayed (+{{ $shipment->delay_hours }}h)
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-[#EDF5EC] text-[#345937] border border-[#CCE2CB]">
                        On Schedule
                    </span>
                @endif
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div class="p-3.5 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC]">
                <span class="text-[#997E67] font-semibold uppercase text-[10px] block mb-1">Departure Schedule</span>
                <span class="font-bold text-[#3D2A1A] font-mono">{{ $shipment->expected_departure->format('d M Y H:i') }}</span>
                @if($shipment->actual_departure)
                <p class="text-[10px] text-[#345937] mt-1">Actual: {{ $shipment->actual_departure->format('d M, H:i') }}</p>
                @endif
            </div>

            <div class="p-3.5 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC]">
                <span class="text-[#997E67] font-semibold uppercase text-[10px] block mb-1">Estimated Arrival (ETA)</span>
                <span class="font-bold text-[#3D2A1A] font-mono">{{ $shipment->estimated_arrival->format('d M Y H:i') }}</span>
                <p class="text-[10px] text-[#997E67] mt-1">Expected: {{ $shipment->expected_arrival->format('d M, H:i') }}</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC]">
                <span class="text-[#997E67] font-semibold uppercase text-[10px] block mb-1">Cargo & Weight</span>
                <span class="font-bold text-[#3D2A1A] line-clamp-1">{{ $shipment->cargo_type }}</span>
                <p class="text-[10px] text-[#997E67] mt-1">{{ number_format($shipment->weight_kg, 1) }} kg • {{ number_format($shipment->volume_cbm, 1) }} CBM</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC]">
                <span class="text-[#997E67] font-semibold uppercase text-[10px] block mb-1">Consignee</span>
                <span class="font-bold text-[#3D2A1A]">{{ $shipment->customer->name }}</span>
                <p class="text-[10px] text-[#997E67] mt-1">{{ $shipment->customer->company_name ?? $shipment->customer->email }}</p>
            </div>
        </div>

        @if($shipment->delay_reason)
        <div class="p-3 rounded-2xl bg-[#FFF6ED] border border-[#F3DAC5] text-xs text-[#8C4626] flex items-start gap-2">
            <i class="fa-solid fa-triangle-exclamation text-[#8C4626] mt-0.5"></i>
            <div>
                <strong>Delay Advisory:</strong> {{ $shipment->delay_reason }}
            </div>
        </div>
        @endif
    </div>

    <!-- Multi-Currency Financial Ledger Card (Neutral Elegance Style) -->
    <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#EDE4DC] shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-[#3D2A1A]">Multi-Currency Financial Ledger</h3>
                <p class="text-[11px] text-[#997E67]">
                    Base currency: <span class="font-bold text-[#3D2A1A] font-mono">{{ $shipment->currency_code }}</span> • Active view: <span class="font-bold text-[#664930] font-mono">{{ $activeCurrencyCode }}</span>
                </p>
            </div>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-[#FAF5EE] text-[#664930] border border-[#EDE4DC]">
                1 USD = {{ $currencyService->getCurrency($activeCurrencyCode)?->exchange_rate_to_usd ?? 1 }} {{ $activeCurrencyCode }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            <div class="p-3 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC]">
                <span class="text-[10px] uppercase font-bold text-[#997E67] block mb-0.5">Freight Cost</span>
                <span class="font-bold text-[#3D2A1A] font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->shipping_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-[#997E67] font-mono mt-0.5">Base: {{ $currencyService->format($shipment->shipping_cost, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC]">
                <span class="text-[10px] uppercase font-bold text-[#997E67] block mb-0.5">Declared Cargo Value</span>
                <span class="font-bold text-[#3D2A1A] font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->declared_value, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-[#997E67] font-mono mt-0.5">Base: {{ $currencyService->format($shipment->declared_value, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC]">
                <span class="text-[10px] uppercase font-bold text-[#997E67] block mb-0.5">Insurance Premium</span>
                <span class="font-bold text-[#3D2A1A] font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->insurance_fee, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-[#997E67] font-mono mt-0.5">Base: {{ $currencyService->format($shipment->insurance_fee, $shipment->currency_code) }}</span>
            </div>

            <div class="p-3 rounded-2xl bg-[#FFDBBB]/40 border border-[#F0CCAC]">
                <span class="text-[10px] uppercase font-bold text-[#664930] block mb-0.5">Total Payable</span>
                <span class="font-extrabold text-[#664930] font-mono text-sm">
                    {{ $currencyService->convertAndFormat($shipment->total_cost, $shipment->currency_code, $activeCurrencyCode) }}
                </span>
                <span class="block text-[10px] text-[#8C7664] font-mono mt-0.5">Base: {{ $currencyService->format($shipment->total_cost, $shipment->currency_code) }}</span>
            </div>
        </div>
    </div>

    <!-- Map & Tracking Container (Neutral Elegance Style) -->
    <div class="rounded-3xl bg-white border border-[#EDE4DC] shadow-sm overflow-hidden space-y-0">
        <!-- Map Header Bar with Legend -->
        <div class="px-5 py-3.5 bg-[#FAF5EE]/90 border-b border-[#EDE4DC] flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#FFDBBB] text-[#664930] border border-[#F0CCAC] flex items-center justify-center text-sm shadow-2xs">
                    <i class="fa-solid fa-map-location-dot"></i>
                </span>
                <div>
                    <h3 class="text-xs font-bold text-[#443224] uppercase tracking-wider">Route Tracking & Position</h3>
                    <p class="text-[11px] text-[#997E67]">
                        Current Location: <strong class="text-[#3D2A1A]">{{ $shipment->current_location_name ?: 'En Route' }}</strong>
                    </p>
                </div>
            </div>

            <!-- Legend Pills -->
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 text-[#6E5947]"><span class="w-2.5 h-2.5 rounded-full bg-[#664930] ring-2 ring-[#FFDBBB]"></span> Origin</span>
                <span class="flex items-center gap-1.5 text-[#6E5947]"><span class="w-2.5 h-2.5 rounded-full bg-[#997E67] ring-2 ring-[#CCBEB1]"></span> Destination</span>
                <span class="flex items-center gap-1.5 text-[#6E5947]"><span class="w-2.5 h-2.5 rounded-full bg-[#8C4626] ring-2 ring-[#FFDBBB]"></span> Vessel Position</span>
            </div>
        </div>

        <!-- Relative Map Frame with Compass & GPS Locate Button Overlay -->
        <div class="relative w-full h-[440px] z-0">
            <!-- Compass North Overlay -->
            <div class="absolute top-3 left-3 z-[400] bg-white/95 backdrop-blur-xs p-1.5 rounded-full shadow-md border border-[#EDE4DC] flex items-center justify-center w-10 h-10 select-none hover:bg-[#FAF5EE] transition cursor-pointer" title="North">
                <div class="relative w-6 h-6 flex items-center justify-center">
                    <div class="w-1 h-3 bg-[#8C4626] rounded-t-sm absolute top-0"></div>
                    <div class="w-1 h-3 bg-[#CCBEB1] rounded-b-sm absolute bottom-0"></div>
                    <span class="text-[8px] font-black text-[#8C4626] absolute -top-1">N</span>
                </div>
            </div>

            <!-- GPS Re-Center Target Button -->
            <button type="button" id="recenterMapBtn" class="absolute bottom-4 right-4 z-[400] bg-white/95 backdrop-blur-xs w-11 h-11 rounded-full shadow-lg border border-[#EDE4DC] flex items-center justify-center text-[#664930] hover:text-[#3D2A1A] hover:scale-105 active:scale-95 transition" title="Center to Vessel">
                <i class="fa-solid fa-crosshairs text-lg"></i>
            </button>

            <!-- Map Div -->
            <div id="shipmentDetailMap" class="w-full h-full"></div>
        </div>

        <!-- Integrated Delivery Status Card (Bottom of Map) -->
        <div class="p-5 sm:p-6 bg-white border-t border-[#EDE4DC] space-y-5">
            <!-- Estimasi Tiba Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-[#EDE4DC]">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#997E67] block mb-0.5">
                        <i class="fa-regular fa-calendar-check mr-1"></i> Estimated Global Delivery
                    </span>
                    <h2 class="text-lg sm:text-xl font-extrabold text-[#3D2A1A]">
                        Estimated arrival: {{ $shipment->estimated_arrival->format('d M') }} – {{ $shipment->estimated_arrival->copy()->addDays(2)->format('d M Y') }}
                    </h2>
                </div>
                <div class="flex items-center gap-2">
                    @if($shipment->delay_status === 'DELAYED')
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#FFF5EC] text-[#8C4626] border border-[#F2DAC4] flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#8C4626]"></span>
                            Delayed (+{{ $shipment->delay_hours }}h)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#EDF5EC] text-[#345937] border border-[#CCE2CB] flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#345937]"></span>
                            On Schedule
                        </span>
                    @endif
                </div>
            </div>

            <!-- 3-Step Milestone Stepper -->
            <div class="py-2">
                <div class="relative flex items-center justify-between max-w-2xl mx-auto px-4 sm:px-10">
                    <div class="absolute left-10 right-10 top-5 h-0.5 bg-[#EDE4DC] -z-0"></div>
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
                    <div class="absolute left-10 top-5 h-0.5 bg-[#664930] -z-0 transition-all duration-500" style="width: {{ $stepperPct }};"></div>

                    <!-- Step 1: Dispatched -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ in_array($shipment->current_status, ['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED']) ? 'bg-[#664930] text-[#FFF8F2] ring-4 ring-[#FFDBBB]' : 'bg-[#FAF5EE] text-[#997E67]' }}">
                            <i class="fa-solid fa-box-archive"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ in_array($shipment->current_status, ['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED']) ? 'text-[#3D2A1A]' : 'text-[#997E67]' }}">
                            Dispatched
                        </span>
                        <span class="text-[10px] text-[#997E67] max-w-[90px] truncate">{{ $shipment->origin->city }}</span>
                    </div>

                    <!-- Step 2: En Route -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ in_array($shipment->current_status, ['IN_TRANSIT', 'DELIVERED']) ? 'bg-[#664930] text-[#FFF8F2] ring-4 ring-[#FFDBBB]' : 'bg-[#FAF5EE] text-[#997E67]' }}">
                            <i class="fa-solid fa-ship"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ in_array($shipment->current_status, ['IN_TRANSIT', 'DELIVERED']) ? 'text-[#3D2A1A]' : 'text-[#997E67]' }}">
                            In Transit
                        </span>
                        <span class="text-[10px] text-[#997E67]">Ocean Voyage</span>
                    </div>

                    <!-- Step 3: Delivered -->
                    <div class="flex flex-col items-center z-10 text-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm shadow-sm transition
                            {{ $shipment->current_status === 'DELIVERED' ? 'bg-[#664930] text-[#FFF8F2] ring-4 ring-[#FFDBBB]' : 'bg-[#FAF5EE] text-[#997E67]' }}">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="text-xs font-bold mt-2 {{ $shipment->current_status === 'DELIVERED' ? 'text-[#3D2A1A]' : 'text-[#997E67]' }}">
                            Delivered
                        </span>
                        <span class="text-[10px] text-[#997E67] max-w-[90px] truncate">{{ $shipment->destination->city }}</span>
                    </div>
                </div>
            </div>

            <!-- Guarantee Badge -->
            <div class="p-3.5 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC] text-xs text-[#523A25] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-[#FFDBBB] text-[#664930] flex items-center justify-center text-xs flex-shrink-0 shadow-2xs">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <strong class="font-semibold text-[#3D2A1A]">Delivery Guarantee:</strong> Verified international SLA coverage by CargoTrace.
                    </div>
                </div>
                <span class="text-[11px] font-bold text-[#664930] bg-[#FFDBBB]/70 px-2.5 py-1 rounded-md border border-[#F0CCAC] shadow-2xs whitespace-nowrap self-start sm:self-auto">
                    Verified Service
                </span>
            </div>

            <!-- Courier & Tracking Card -->
            <div class="p-4 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-white border border-[#EDE4DC] shadow-2xs flex items-center justify-center text-[#664930] text-xl">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#3D2A1A]">{{ $shipment->carrier->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#FFDBBB]/50 text-[#664930] font-semibold uppercase">{{ $shipment->carrier->service_type }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="font-mono text-xs font-bold text-[#523A25]" id="trackingNumSpan">{{ $shipment->shipment_number }}</span>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $shipment->shipment_number }}'); this.querySelector('span').innerText='Copied!'; setTimeout(() => this.querySelector('span').innerText='Copy', 2000);"
                                    class="text-[11px] text-[#664930] hover:text-[#3D2A1A] font-semibold flex items-center gap-1 transition">
                                <i class="fa-regular fa-copy"></i>
                                <span>Copy</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-[#7A6350]">Route: <strong class="text-[#3D2A1A]">{{ $shipment->origin->city }} ({{ $shipment->origin->code }}) → {{ $shipment->destination->city }} ({{ $shipment->destination->code }})</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations & SLA Control Panel -->
    @can('update', $shipment)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Milestone Transition Form -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#EDE4DC] shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-[#443224] uppercase tracking-wider">Lifecycle Progression</h3>
            <p class="text-[11px] text-[#997E67]">Transition shipment status. Current status: <strong>{{ $shipment->current_status }}</strong></p>

            @if($shipment->isTerminal())
            <div class="p-3 rounded-2xl bg-[#FAF5EE] text-xs text-[#997E67] border border-[#EDE4DC]">
                Consignment is in a terminal status ({{ $shipment->current_status }}) and cannot be changed.
            </div>
            @else
            <form action="{{ route('shipments.status', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Target Next Status</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] focus:outline-none focus:border-[#997E67]">
                        @foreach(\App\Models\Shipment::VALID_TRANSITIONS[$shipment->current_status] ?? [] as $next)
                            <option value="{{ $next }}">{{ str_replace('_', ' ', $next) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Notes</label>
                    <input type="text" name="notes" placeholder="Transition notes..." class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] focus:outline-none focus:border-[#997E67]">
                </div>
                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-[#664930] hover:bg-[#523A25] text-[#FFF8F2] font-semibold text-xs shadow-sm transition border border-[#523A25]">
                    Update Status
                </button>
            </form>
            @endif
        </div>

        <!-- Add Checkpoint Form -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#EDE4DC] shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-[#443224] uppercase tracking-wider">Add Telemetry Waypoint</h3>
            <p class="text-[11px] text-[#997E67]">Record verified location coordinates and integrated live weather.</p>

            <form action="{{ route('shipments.tracking.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Status</label>
                        <select name="status" required class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] focus:outline-none focus:border-[#997E67]">
                            <option value="IN_TRANSIT">In Transit</option>
                            <option value="PORT_ARRIVAL">Port Arrival</option>
                            <option value="CUSTOMS_CLEARANCE">Customs Clearance</option>
                            <option value="DEPARTED">Departed</option>
                            <option value="DELIVERED">Delivered</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Location Name</label>
                        <input type="text" name="location_name" required placeholder="Port or oceanic coordinate" class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] focus:outline-none focus:border-[#997E67]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Latitude</label>
                        <input type="number" step="any" name="latitude" placeholder="e.g. 1.2902" class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Longitude</label>
                        <input type="number" step="any" name="longitude" placeholder="e.g. 103.8519" class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Description / Notes</label>
                    <input type="text" name="description" required placeholder="Waypoint log details..." class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] focus:outline-none">
                </div>

                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-[#523A25] hover:bg-[#3D2A1A] text-[#FFF8F2] font-semibold text-xs shadow-sm transition">
                    Save Checkpoint
                </button>
            </form>
        </div>
    </div>
    @endcan

    <!-- Exceptions SLA Center -->
    <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#EDE4DC] shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#3D2A1A]">SLA Incidents & Exceptions Management</h3>
                <p class="text-[11px] text-[#997E67]">Customs inspections, weather storms, and biosecurity protocols</p>
            </div>

            @can('update', $shipment)
            <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.toggle('hidden')" class="px-3 py-1.5 rounded-xl bg-[#FFF5EC] hover:bg-[#FDEBD9] text-[#8C4626] border border-[#F2DAC4] text-xs font-semibold transition">
                + Report New Incident
            </button>
            @endcan
        </div>

        <!-- Hidden Drawer to Raise Exception -->
        <div id="raiseExceptionDrawer" class="hidden p-4 rounded-2xl bg-[#FFF6ED] border border-[#F2DAC4] space-y-3">
            <h4 class="text-xs font-bold uppercase text-[#8C4626]">Record Incident</h4>
            <form action="{{ route('shipments.exceptions.store', $shipment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Incident Type</label>
                        <select name="exception_type" required class="w-full px-3 py-1.5 text-xs bg-white border border-[#EDE4DC] rounded-xl">
                            <option value="CUSTOMS_HOLD">Customs Hold</option>
                            <option value="WEATHER_DELAY">Weather Delay</option>
                            <option value="PORT_CONGESTION">Port Congestion</option>
                            <option value="DAMAGED">Damaged Cargo</option>
                            <option value="MISROUTED">Misrouted</option>
                            <option value="DOCUMENTATION_ISSUE">Documentation Issue</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Severity</label>
                        <select name="severity" required class="w-full px-3 py-1.5 text-xs bg-white border border-[#EDE4DC] rounded-xl">
                            <option value="LOW">Low (0 - 6 hours)</option>
                            <option value="MEDIUM" selected>Medium (6 - 24 hours)</option>
                            <option value="HIGH">High (> 24 hours)</option>
                            <option value="CRITICAL">Critical</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Delay Impact (Hours)</label>
                        <input type="number" name="delay_impact_hours" min="0" value="12" class="w-full px-3 py-1.5 text-xs bg-white border border-[#EDE4DC] rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#6E5947] mb-1">Root Cause</label>
                    <textarea name="root_cause" required rows="2" placeholder="Description of the incident..." class="w-full px-3 py-1.5 text-xs bg-white border border-[#EDE4DC] rounded-xl"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('raiseExceptionDrawer').classList.add('hidden')" class="px-3 py-1 text-xs text-[#997E67]">Cancel</button>
                    <button type="submit" class="px-4 py-1.5 rounded-xl bg-[#8C4626] text-white font-semibold text-xs">Save Incident</button>
                </div>
            </form>
        </div>

        <!-- Exceptions List -->
        <div class="space-y-2">
            @forelse($shipment->exceptions as $exc)
            <div class="p-3.5 rounded-2xl {{ $exc->isOpen() ? 'bg-[#FFF5EC] border border-[#F2DAC4]' : 'bg-[#FAF6F1] border border-[#EDE4DC]' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-[#3D2A1A]">{{ $exc->exception_type }}</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold {{ $exc->isOpen() ? 'bg-[#FDE7E3] text-[#963E32]' : 'bg-[#EDF5EC] text-[#345937]' }}">
                            {{ $exc->isOpen() ? 'OPEN' : 'RESOLVED' }}
                        </span>
                        <span class="text-[10px] text-[#997E67] font-mono">{{ $exc->severity }}</span>
                    </div>
                    <p class="text-[#6E5947] mt-1">{{ $exc->root_cause }}</p>
                    @if($exc->resolution_notes)
                    <p class="text-[11px] text-[#345937] mt-1">Resolution: {{ $exc->resolution_notes }}</p>
                    @endif
                </div>

                @if($exc->isOpen() && Auth::user()->canManageShipments())
                <form action="{{ route('exceptions.resolve', $exc->id) }}" method="POST" class="flex items-center gap-2 shrink-0">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="resolution_notes" required placeholder="Resolution summary..." class="px-2.5 py-1 text-xs bg-white border border-[#EDE4DC] rounded-lg">
                    <button type="submit" class="px-3 py-1 rounded-lg bg-[#345937] text-white font-semibold text-xs">
                        Resolve
                    </button>
                </form>
                @endif
            </div>
            @empty
            <p class="text-xs text-[#997E67] text-center py-2">No active exceptions for this shipment.</p>
            @endforelse
        </div>
    </div>

    <!-- Timeline & Manifest -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Checkpoint Timeline -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#EDE4DC] shadow-sm lg:col-span-2 space-y-4">
            <h3 class="text-xs font-bold text-[#443224] uppercase tracking-wider">Tracking Timeline</h3>

            <div class="relative pl-6 space-y-4 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-[#EDE4DC]">
                @forelse($shipment->trackingEvents as $ev)
                <div class="relative">
                    <div class="absolute -left-6 top-1 w-5 h-5 rounded-full flex items-center justify-center text-[10px]
                        {{ $ev->is_exception ? 'bg-[#8C4626] text-white' : 'bg-[#664930] text-[#FFF8F2]' }}">
                        <i class="fa-solid fa-check text-[9px]"></i>
                    </div>

                    <div class="p-3.5 rounded-2xl {{ $ev->is_exception ? 'bg-[#FFF5EC] border border-[#F2DAC4]' : 'bg-[#FAF6F1] border border-[#EDE4DC]' }}">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="text-xs font-bold text-[#3D2A1A]">{{ $ev->status }} • {{ $ev->location_name }}</span>
                            <span class="text-[10px] font-mono text-[#997E67]">{{ $ev->event_time->format('d M, H:i') }}</span>
                        </div>
                        <p class="text-xs text-[#6E5947]">{{ $ev->description }}</p>
                        @if($ev->weather_summary)
                        <span class="inline-block mt-1 text-[10px] text-[#664930] bg-[#FFDBBB]/50 px-2 py-0.5 rounded border border-[#F0CCAC]">
                            {{ $ev->weather_summary }}
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-[#997E67]">No checkpoint milestones recorded.</p>
                @endforelse
            </div>
        </div>

        <!-- Manifest Items -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white/95 border border-[#EDE4DC] shadow-sm space-y-3">
            <h3 class="text-xs font-bold text-[#443224] uppercase tracking-wider">Manifest Items</h3>

            <div class="space-y-2">
                @forelse($shipment->items as $item)
                <div class="p-3 rounded-2xl bg-[#FAF6F1] border border-[#EDE4DC] text-xs">
                    <div class="font-bold text-[#3D2A1A]">{{ $item->description }}</div>
                    <div class="grid grid-cols-2 gap-1 mt-1 text-[11px] text-[#7A6350]">
                        <div>Qty: <strong>{{ $item->quantity }}</strong></div>
                        <div>Weight: <strong>{{ number_format($item->weight_kg, 1) }} kg</strong></div>
                        <div>Package: {{ $item->package_type }}</div>
                        <div>Value: {{ $currencyService->format($item->declared_value, $shipment->currency_code) }}</div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-[#997E67]">Consolidated cargo container.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Neutral Elegance Style Callout Bubble & Pin styles */
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
        color: #664930;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(102, 73, 48, 0.15);
        border: 1px solid #CCBEB1;
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
        background: linear-gradient(135deg, #664930 0%, #3D2A1A 100%);
        border: 2.5px solid #FFDBBB;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFDBBB;
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
        let calloutStatus = "Processing Order";
        if (shipmentStatus === 'IN_TRANSIT') {
            calloutStatus = "Sailing In Transit";
        } else if (shipmentStatus === 'DELIVERED') {
            calloutStatus = "Order Delivered";
        } else if (shipmentStatus === 'PICKED_UP') {
            calloutStatus = "Cargo Loaded";
        } else if (shipmentStatus === 'BOOKED') {
            calloutStatus = "Ready for Departure";
        }

        mapData.forEach(function (pt) {
            latLngs.push([pt.lat, pt.lng]);

            if (pt.type === 'current') {
                const vehicleHtml = `
                    <div class="vehicle-callout-wrapper">
                        <div class="shopee-bubble">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#8C4626] inline-block animate-ping"></span>
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
                        <strong style="color: #3D2A1A; font-size: 13px;">${pt.title}</strong><br>
                        <span style="color: #7A6350;">${pt.location || ''}</span>
                        ${pt.weather ? `<br><span style="color: #664930; font-weight: 600; font-size: 11px;">☀️ Weather: ${pt.weather}</span>` : ''}
                    </div>
                `;
                vehicleMarker.bindPopup(popupContent);

            } else if (pt.type === 'origin') {
                const originIcon = L.divIcon({
                    className: 'custom-origin-div',
                    html: `<div class="port-pin bg-[#664930]"><i class="fa-solid fa-anchor"></i></div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
                const m = L.marker([pt.lat, pt.lng], { icon: originIcon }).addTo(map);
                m.bindPopup(`<b>Port of Origin:</b> ${pt.location}<br><span style="font-size:11px;color:#7A6350;">Time: ${pt.time || '-'}</span>`);

            } else if (pt.type === 'destination') {
                const destIcon = L.divIcon({
                    className: 'custom-dest-div',
                    html: `<div class="port-pin bg-[#997E67]"><i class="fa-solid fa-location-dot"></i></div>`,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });
                const m = L.marker([pt.lat, pt.lng], { icon: destIcon }).addTo(map);
                m.bindPopup(`<b>Port of Destination:</b> ${pt.location}`);

            } else {
                // Waypoint / Exception Checkpoint
                const isExc = pt.type === 'exception';
                const wpIcon = L.circleMarker([pt.lat, pt.lng], {
                    radius: isExc ? 7 : 5,
                    fillColor: isExc ? '#8C4626' : '#997E67',
                    color: '#ffffff',
                    weight: 2,
                    fillOpacity: 0.95
                }).addTo(map);

                let popupContent = `<b>${pt.title}</b><br>${pt.location}`;
                if (pt.time) popupContent += `<br><span style="color:#7A6350;font-size:11px;">Time: ${pt.time}</span>`;
                if (pt.weather) popupContent += `<br><span style="color:#664930;font-size:11px;">Weather: ${pt.weather}</span>`;
                wpIcon.bindPopup(popupContent);
            }
        });

        // Neutral Elegance Polyline
        if (latLngs.length > 1) {
            L.polyline(latLngs, {
                color: '#664930',
                weight: 5,
                opacity: 0.85
            }).addTo(map);

            L.polyline(latLngs, {
                color: '#FFDBBB',
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