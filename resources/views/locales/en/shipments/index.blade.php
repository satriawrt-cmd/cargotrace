@extends('layouts.app')

@section('title', 'Shipments')

@section('content')
<div class="space-y-6">
    <!-- Page Header (Neutral Elegance Style) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#3D2A1A]">Shipments</h1>
            <p class="text-xs text-[#7A6350] mt-0.5">
                {{ $shipments->total() }} commercial consignments across world trade corridors
            </p>
        </div>

        @if(Auth::user()->canManageShipments())
        <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#664930] hover:bg-[#523A25] text-[#FFF8F2] font-semibold text-xs rounded-xl shadow-sm transition border border-[#523A25]">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Create Shipment</span>
        </a>
        @endif
    </div>

    <!-- Unified Search & Filter Bar (Neutral Elegance Style) -->
    <div class="bg-white/95 rounded-2xl border border-[#EDE4DC] shadow-sm p-4">
        <form action="{{ route('shipments.index') }}" method="GET" class="space-y-3">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Search input with inner icon -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-[#997E67]">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search shipments, cargo, cities..."
                           class="w-full pl-9 pr-4 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#3D2A1A] placeholder-[#B5A190] focus:outline-none focus:bg-white focus:border-[#997E67] transition">
                </div>

                <!-- Status Filter Dropdown -->
                <div class="w-full sm:w-44">
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#443224] focus:outline-none focus:bg-white focus:border-[#997E67] transition">
                        <option value="">All Statuses</option>
                        <option value="CREATED" {{ ($filters['status'] ?? '') === 'CREATED' ? 'selected' : '' }}>Created</option>
                        <option value="BOOKED" {{ ($filters['status'] ?? '') === 'BOOKED' ? 'selected' : '' }}>Booked</option>
                        <option value="PICKED_UP" {{ ($filters['status'] ?? '') === 'PICKED_UP' ? 'selected' : '' }}>Picked Up</option>
                        <option value="IN_TRANSIT" {{ ($filters['status'] ?? '') === 'IN_TRANSIT' ? 'selected' : '' }}>In Transit</option>
                        <option value="DELIVERED" {{ ($filters['status'] ?? '') === 'DELIVERED' ? 'selected' : '' }}>Delivered</option>
                        <option value="CANCELLED" {{ ($filters['status'] ?? '') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Carrier Filter Dropdown -->
                <div class="w-full sm:w-44">
                    <select name="carrier_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#443224] focus:outline-none focus:bg-white focus:border-[#997E67] transition">
                        <option value="">All Carriers</option>
                        @foreach($carriers as $c)
                            <option value="{{ $c->id }}" {{ ($filters['carrier_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Delay Status Filter -->
                <div class="w-full sm:w-40">
                    <select name="delay_status" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-[#FAF5EE] border border-[#EDE4DC] rounded-xl text-[#443224] focus:outline-none focus:bg-white focus:border-[#997E67] transition">
                        <option value="">All ETA Schedules</option>
                        <option value="ON_TIME" {{ ($filters['delay_status'] ?? '') === 'ON_TIME' ? 'selected' : '' }}>On Time</option>
                        <option value="DELAYED" {{ ($filters['delay_status'] ?? '') === 'DELAYED' ? 'selected' : '' }}>Delayed Only</option>
                    </select>
                </div>

                @if(!empty(array_filter($filters ?? [])))
                <a href="{{ route('shipments.index') }}" class="p-2 text-[#997E67] hover:text-[#3D2A1A] text-xs text-center" title="Reset Filters">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Clean Neutral Elegance Data Table -->
    <div class="bg-white/95 rounded-2xl border border-[#EDE4DC] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#EDE4DC] bg-[#F8F2EA] text-[11px] font-semibold text-[#7A6451]">
                        <th class="py-3 px-5">Shipment</th>
                        <th class="py-3 px-4">Trade Route</th>
                        <th class="py-3 px-4">Carrier</th>
                        <th class="py-3 px-4">Cargo & Weight</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Freight Cost ({{ $activeCurrencyCode }})</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2EAE2] text-[#6E5947]">
                    @forelse($shipments as $shipment)
                    <tr class="hover:bg-[#FAF5EE]/80 transition">
                        <!-- Shipment & Number -->
                        <td class="py-3.5 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#FFDBBB] text-[#664930] border border-[#F0CCAC] flex items-center justify-center shrink-0 shadow-2xs">
                                    <i class="fa-solid fa-box text-sm"></i>
                                </div>
                                <div>
                                    <a href="{{ route('shipments.show', $shipment->id) }}" class="font-bold text-[#3D2A1A] hover:text-[#664930] font-mono transition">
                                        {{ $shipment->shipment_number }}
                                    </a>
                                    <p class="text-[11px] text-[#997E67]">{{ $shipment->customer->name }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Trade Route -->
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-[#443224] flex items-center gap-1.5">
                                <span>{{ $shipment->origin->city }}</span>
                                <i class="fa-solid fa-arrow-right text-[10px] text-[#997E67]"></i>
                                <span>{{ $shipment->destination->city }}</span>
                            </div>
                            <span class="text-[10px] text-[#997E67]">{{ $shipment->origin->country }} → {{ $shipment->destination->country }}</span>
                        </td>

                        <!-- Carrier -->
                        <td class="py-3.5 px-4">
                            <span class="font-medium text-[#443224]">{{ $shipment->carrier->name }}</span>
                            <span class="block text-[10px] text-[#997E67]">{{ $shipment->carrier->service_type }}</span>
                        </td>

                        <!-- Cargo -->
                        <td class="py-3.5 px-4">
                            <span class="font-medium text-[#443224] line-clamp-1 max-w-xs">{{ $shipment->cargo_type }}</span>
                            <span class="text-[10px] text-[#997E67]">{{ number_format($shipment->weight_kg, 0) }} kg</span>
                        </td>

                        <!-- Status Badge (Neutral Elegance Style) -->
                        <td class="py-3.5 px-4 text-center">
                            @if($shipment->current_status === 'DELIVERED')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#EDF5EC] text-[#345937] border border-[#CCE2CB]">
                                    Delivered
                                </span>
                            @elseif($shipment->current_status === 'IN_TRANSIT')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#F5EDE3] text-[#72543B] border border-[#E2D2C0]">
                                    In Transit
                                </span>
                            @elseif($shipment->current_status === 'BOOKED' || $shipment->current_status === 'PICKED_UP')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FFF3E5] text-[#8C582B] border border-[#F4D7B8]">
                                    {{ str_replace('_', ' ', $shipment->current_status) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF4ED] text-[#6E5947] border border-[#E8DCCF]">
                                    {{ str_replace('_', ' ', $shipment->current_status) }}
                                </span>
                            @endif

                            @if($shipment->delay_status === 'DELAYED')
                            <span class="inline-block ml-1 px-1.5 py-0.2 rounded text-[10px] font-bold bg-[#FFF5EC] text-[#8C4626] border border-[#F2DAC4]">
                                +{{ $shipment->delay_hours }}h
                            </span>
                            @endif
                        </td>

                        <!-- Cost in Active Currency -->
                        <td class="py-3.5 px-4 text-right">
                            <span class="font-bold text-[#3D2A1A] font-mono text-xs">
                                {{ $currencyService->convertAndFormat($shipment->total_cost, $shipment->currency_code, $activeCurrencyCode) }}
                            </span>
                            <span class="block text-[10px] text-[#997E67] font-mono">
                                Base: {{ $currencyService->format($shipment->total_cost, $shipment->currency_code) }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('shipments.show', $shipment->id) }}" class="p-1.5 rounded-lg text-[#664930] hover:text-[#3D2A1A] bg-[#F5EDE4] hover:bg-[#EADED1] transition" title="Details">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                                @can('update', $shipment)
                                <a href="{{ route('shipments.edit', $shipment->id) }}" class="p-1.5 rounded-lg text-[#7A5A41] hover:text-[#3D2A1A] bg-[#FAF5EE] hover:bg-[#EDE0D0] transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                @endcan
                                @can('delete', $shipment)
                                <form action="{{ route('shipments.destroy', $shipment->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this shipment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-[#8C4626] hover:text-[#664930] hover:bg-[#FDF2F0] transition" title="Delete">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[#997E67]">
                            <div class="w-10 h-10 rounded-full bg-[#FAF5EE] text-[#997E67] flex items-center justify-center mx-auto mb-2 text-base border border-[#EDE4DC]">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <p class="text-xs font-semibold text-[#443224]">No shipments found</p>
                            <p class="text-[11px] text-[#997E67] mt-0.5">Try adjusting your filters or register a new shipment.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-3 border-t border-[#EDE4DC] bg-[#FAF5EE]/50">
            {{ $shipments->links() }}
        </div>
    </div>
</div>
@endsection