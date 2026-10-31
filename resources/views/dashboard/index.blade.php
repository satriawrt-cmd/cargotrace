@extends('layouts.app')

@section('title', 'Operations & Visibility Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header with Quick Action & Currency Indicator -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                <span>Shipment Visibility Dashboard</span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-500/10 text-brand-400 border border-brand-500/20">
                    Live Telemetry
                </span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Welcome back, <strong class="text-slate-200">{{ Auth::user()->name }}</strong>. All financial costs displayed in
                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-amber-500/10 text-amber-300 font-bold font-mono border border-amber-500/20">
                    {{ $activeCurrencyCode }}
                </span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(Auth::user()->canManageShipments())
            <a href="{{ route('shipments.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-500 hover:to-brand-400 text-white font-semibold text-xs shadow-lg shadow-brand-500/20 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> New Global Shipment
            </a>
            @endif
            <a href="{{ route('public.track') }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-earth-americas text-brand-400"></i> Public Tracker
            </a>
        </div>
    </div>

    <!-- 6 KPI Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Shipments -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Consignments</span>
                <i class="fa-solid fa-boxes-stacked text-brand-400 text-xs"></i>
            </div>
            <div class="text-2xl font-black text-white font-mono">{{ $totalShipments }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Across 6 continents</div>
        </div>

        <!-- In Transit -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">In Transit</span>
                <i class="fa-solid fa-ship text-blue-400 text-xs animate-pulse"></i>
            </div>
            <div class="text-2xl font-black text-blue-400 font-mono">{{ $inTransitCount }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Active voyages</div>
        </div>

        <!-- Delivered -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Delivered</span>
                <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
            </div>
            <div class="text-2xl font-black text-emerald-400 font-mono">{{ $deliveredCount }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Discharged & cleared</div>
        </div>

        <!-- Delayed -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Delayed</span>
                <i class="fa-solid fa-clock-rotate-left text-amber-400 text-xs"></i>
            </div>
            <div class="text-2xl font-black text-amber-400 font-mono">{{ $delayedCount }}</div>
            <div class="text-[10px] text-slate-500 mt-1">ETA extended</div>
        </div>

        <!-- Open Exceptions -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Open Exceptions</span>
                <i class="fa-solid fa-triangle-exclamation text-red-400 text-xs"></i>
            </div>
            <div class="text-2xl font-black text-red-400 font-mono">{{ $openExceptionsCount }}</div>
            <div class="text-[10px] text-slate-500 mt-1">Under investigation</div>
        </div>

        <!-- On-Time Rate KPI -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">On-Time KPI</span>
                <i class="fa-solid fa-chart-line text-cyan-400 text-xs"></i>
            </div>
            <div class="text-2xl font-black text-cyan-400 font-mono">{{ $onTimeRate }}%</div>
            <div class="text-[10px] text-slate-500 mt-1">Avg delay: {{ $avgDelayHours }}h</div>
        </div>
    </div>

    <!-- Active Exceptions Banner (if any) -->
    @if(count($activeExceptions) > 0)
    <div class="p-5 rounded-2xl bg-gradient-to-r from-red-950/40 via-slate-900 to-slate-900 border border-red-500/30 shadow-xl">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2 text-red-400 font-bold text-xs uppercase tracking-wider">
                <i class="fa-solid fa-triangle-exclamation animate-bounce"></i>
                <span>Active Operational Exceptions Requiring Attention ({{ count($activeExceptions) }})</span>
            </div>
            <span class="text-[10px] text-slate-400 font-mono">Immediate SLA Action</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($activeExceptions as $exc)
            <div class="p-3.5 rounded-xl bg-slate-950/70 border border-red-500/20 flex items-start justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-white">{{ $exc->shipment->shipment_number }}</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase
                            {{ $exc->severity === 'CRITICAL' ? 'bg-red-500 text-white' : '' }}
                            {{ $exc->severity === 'HIGH' ? 'bg-orange-500/20 text-orange-400 border border-orange-500/30' : '' }}
                            {{ $exc->severity === 'MEDIUM' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : '' }}
                            {{ $exc->severity === 'LOW' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : '' }}">
                            {{ $exc->exception_type }} ({{ $exc->severity }})
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 mt-1 line-clamp-1">{{ $exc->root_cause }}</p>
                    <p class="text-[10px] text-slate-500 mt-1">Detected: {{ $exc->detected_at->diffForHumans() }}</p>
                </div>
                <a href="{{ route('shipments.show', $exc->shipment_id) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold shrink-0 transition">
                    Inspect <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Visual Analytics: Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: Status Distribution -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-brand-400"></i> Shipments by Lifecycle Status
                    </h3>
                </div>
                <div class="relative h-56 flex items-center justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 text-center mt-3">Live breakdown across verified stages</p>
        </div>

        <!-- Chart 2: Shipments by Carrier -->
        <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-simple text-cyan-400"></i> Volume by Ocean & Air Freight Carriers
                    </h3>
                    <span class="text-[10px] text-slate-500 uppercase font-mono">Consignment Load</span>
                </div>
                <div class="relative h-56">
                    <canvas id="carrierChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 text-center mt-3">Distribution across active global carriers</p>
        </div>
    </div>

    <!-- Recent Shipments Table -->
    <div class="p-6 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-brand-400"></i> Recent Worldwide Consignments
                </h3>
                <p class="text-xs text-slate-400">Latest active consignments across international trade routes</p>
            </div>
            <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-brand-400 hover:text-brand-300 flex items-center gap-1 transition">
                <span>View Full Registry</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Shipment #</th>
                        <th class="py-3 px-4">Carrier</th>
                        <th class="py-3 px-4">Worldwide Trade Route</th>
                        <th class="py-3 px-4">Cargo & Weight</th>
                        <th class="py-3 px-4">Status & ETA</th>
                        <th class="py-3 px-4 text-right">Freight Rate ({{ $activeCurrencyCode }})</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($recentShipments as $s)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-white">
                            <a href="{{ route('shipments.show', $s->id) }}" class="text-brand-400 hover:text-brand-300 hover:underline">
                                {{ $s->shipment_number }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-200">
                            {{ $s->carrier->name }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-slate-200">{{ $s->origin->city }}</span>
                                <i class="fa-solid fa-arrow-right text-[9px] text-slate-600"></i>
                                <span class="font-semibold text-slate-200">{{ $s->destination->city }}</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $s->origin->country }} → {{ $s->destination->country }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="line-clamp-1 max-w-xs">{{ $s->cargo_type }}</div>
                            <span class="text-[10px] text-slate-500">{{ number_format($s->weight_kg, 0) }} kg</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $s->current_status === 'DELIVERED' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                                    {{ $s->current_status === 'IN_TRANSIT' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : '' }}
                                    {{ in_array($s->current_status, ['CREATED', 'BOOKED', 'PICKED_UP']) ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : '' }}">
                                    {{ str_replace('_', ' ', $s->current_status) }}
                                </span>
                                @if($s->delay_status === 'DELAYED')
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-red-500/20 text-red-400 border border-red-500/30">
                                    +{{ $s->delay_hours }}h
                                </span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-500">ETA: {{ $s->estimated_arrival->format('M d') }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                            {{ $currencyService->convertAndFormat($s->total_cost, $s->currency_code, $activeCurrencyCode) }}
                            <div class="text-[9px] text-slate-500 font-normal">
                                Orig: {{ $currencyService->format($s->total_cost, $s->currency_code) }}
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Inspect Voyage">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">No shipments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Status Doughnut Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
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
                    backgroundColor: ['#64748b', '#f59e0b', '#8b5cf6', '#3b82f6', '#10b981'],
                    borderColor: '#0f172a',
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 11 }, padding: 12 }
                    }
                },
                cutout: '68%'
            }
        });

        // 2. Carrier Bar Chart
        const carrierCtx = document.getElementById('carrierChart').getContext('2d');
        new Chart(carrierCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($carrierCounts)) !!},
                datasets: [{
                    label: 'Shipment Count',
                    data: {!! json_encode(array_values($carrierCounts)) !!},
                    backgroundColor: '#2563eb',
                    borderRadius: 8,
                    hoverBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#64748b', stepSize: 1 },
                        grid: { color: '#1e293b' }
                    },
                    x: {
                        ticks: { color: '#94a3b8', font: { size: 10 } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush
