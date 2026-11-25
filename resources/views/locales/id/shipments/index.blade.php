@extends('layouts.app')

@section('title', 'Daftar Pengiriman')

@section('content')
<div class="space-y-6">
    <!-- Page Header (Ocean Maritime Style) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">Daftar Pengiriman</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $shipments->total() }} konsinyasi di seluruh rute perdagangan dunia
            </p>
        </div>

        @if(Auth::user()->canManageShipments())
        <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#1E3A8A] hover:bg-[#1D4ED8] text-white font-semibold text-xs rounded-xl shadow-sm transition border border-sky-400/30">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Pengiriman</span>
        </a>
        @endif
    </div>

    <!-- Unified Search & Filter Bar (Ocean Maritime Style) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form action="{{ route('shipments.index') }}" method="GET" class="space-y-3">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Search input with inner icon -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari pengiriman, kargo, kota..."
                           class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-[#0F172A] placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#1E3A8A] transition">
                </div>

                <!-- Status Filter Dropdown -->
                <div class="w-full sm:w-44">
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:bg-white focus:border-[#1E3A8A] transition">
                        <option value="">Semua Status</option>
                        <option value="CREATED" {{ ($filters['status'] ?? '') === 'CREATED' ? 'selected' : '' }}>Dibuat</option>
                        <option value="BOOKED" {{ ($filters['status'] ?? '') === 'BOOKED' ? 'selected' : '' }}>Terjadwal</option>
                        <option value="PICKED_UP" {{ ($filters['status'] ?? '') === 'PICKED_UP' ? 'selected' : '' }}>Diambil</option>
                        <option value="IN_TRANSIT" {{ ($filters['status'] ?? '') === 'IN_TRANSIT' ? 'selected' : '' }}>Dalam Perjalanan</option>
                        <option value="DELIVERED" {{ ($filters['status'] ?? '') === 'DELIVERED' ? 'selected' : '' }}>Terkirim</option>
                        <option value="CANCELLED" {{ ($filters['status'] ?? '') === 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <!-- Carrier Filter Dropdown -->
                <div class="w-full sm:w-44">
                    <select name="carrier_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:bg-white focus:border-[#1E3A8A] transition">
                        <option value="">Semua Operator</option>
                        @foreach($carriers as $c)
                            <option value="{{ $c->id }}" {{ ($filters['carrier_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Delay Status Filter -->
                <div class="w-full sm:w-40">
                    <select name="delay_status" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:bg-white focus:border-[#1E3A8A] transition">
                        <option value="">Semua Jadwal ETA</option>
                        <option value="ON_TIME" {{ ($filters['delay_status'] ?? '') === 'ON_TIME' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="DELAYED" {{ ($filters['delay_status'] ?? '') === 'DELAYED' ? 'selected' : '' }}>Hanya yang Terlambat</option>
                    </select>
                </div>

                @if(!empty(array_filter($filters ?? [])))
                <a href="{{ route('shipments.index') }}" class="p-2 text-slate-400 hover:text-slate-700 text-xs text-center" title="Reset Filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Clean Ocean Maritime Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold text-slate-600">
                        <th class="py-3 px-5">Pengiriman</th>
                        <th class="py-3 px-4">Rute Perdagangan</th>
                        <th class="py-3 px-4">Operator</th>
                        <th class="py-3 px-4">Kargo & Berat</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Biaya Kirim ({{ $activeCurrencyCode }})</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($shipments as $shipment)
                    <tr class="hover:bg-slate-50/80 transition">
                        <!-- Shipment & Number -->
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1E3A8A] border border-blue-100 flex items-center justify-center shrink-0 shadow-2xs">
                                    <i class="fa-solid fa-box text-sm"></i>
                                </div>
                                <div>
                                    <a href="{{ route('shipments.show', $shipment->id) }}" class="font-bold text-[#0F172A] hover:text-[#1E3A8A] font-mono transition">
                                        {{ $shipment->shipment_number }}
                                    </a>
                                    <p class="text-[11px] text-slate-400">{{ $shipment->customer->name }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Trade Route -->
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                                <span>{{ $shipment->origin->city }}</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
                                <span>{{ $shipment->destination->city }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400">{{ $shipment->origin->country }} → {{ $shipment->destination->country }}</span>
                        </td>

                        <!-- Carrier -->
                        <td class="py-3.5 px-4">
                            <span class="font-medium text-slate-800">{{ $shipment->carrier->name }}</span>
                            <span class="block text-[10px] text-slate-400">{{ $shipment->carrier->service_type }}</span>
                        </td>

                        <!-- Cargo -->
                        <td class="py-3.5 px-4">
                            <span class="font-medium text-slate-800 line-clamp-1 max-w-xs">{{ $shipment->localized_cargo_type }}</span>
                            <span class="text-[10px] text-slate-400">{{ number_format($shipment->weight_kg, 0) }} kg</span>
                        </td>

                        <!-- Status Badge (Ocean Maritime Style) -->
                        <td class="py-3.5 px-4 text-center">
                            @if($shipment->current_status === 'DELIVERED')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Terkirim
                                </span>
                            @elseif($shipment->current_status === 'IN_TRANSIT')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                    Dalam Perjalanan
                                </span>
                            @elseif($shipment->current_status === 'BOOKED' || $shipment->current_status === 'PICKED_UP')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $shipment->current_status === 'BOOKED' ? 'Terjadwal' : 'Diambil' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $shipment->current_status === 'CREATED' ? 'Dibuat' : ($shipment->current_status === 'CANCELLED' ? 'Dibatalkan' : $shipment->current_status) }}
                                </span>
                            @endif

                            @if($shipment->delay_status === 'DELAYED')
                            <span class="inline-block ml-1 px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                +{{ $shipment->delay_hours }}j
                            </span>
                            @endif
                        </td>

                        <!-- Cost in Active Currency -->
                        <td class="py-3.5 px-4 text-right">
                            <span class="font-bold text-[#0F172A] font-mono text-xs">
                                {{ $currencyService->convertAndFormat($shipment->total_cost, $shipment->currency_code, $activeCurrencyCode) }}
                            </span>
                            <span class="block text-[10px] text-slate-400 font-mono">
                                Asli: {{ $currencyService->format($shipment->total_cost, $shipment->currency_code) }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('shipments.show', $shipment->id) }}" class="p-1.5 rounded-lg text-[#1E3A8A] hover:text-[#0B132B] bg-sky-50 hover:bg-sky-100 transition" title="Rincian">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                                @can('update', $shipment)
                                <a href="{{ route('shipments.edit', $shipment->id) }}" class="p-1.5 rounded-lg text-slate-600 hover:text-[#0F172A] bg-slate-100 hover:bg-slate-200 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                @endcan
                                @can('delete', $shipment)
                                <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengiriman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-rose-600 hover:text-rose-800 hover:bg-rose-50 transition" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-2 text-base border border-slate-200">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-700">Tidak ada pengiriman ditemukan</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Coba sesuaikan kriteria pencarian atau tambahkan pengiriman baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/50">
            {{ $shipments->links() }}
        </div>
    </div>
</div>
@endsection