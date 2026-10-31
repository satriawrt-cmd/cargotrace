@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Operations Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Overview of real-time shipment visibility across worldwide trade routes.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(Auth::user()->canManageShipments())
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Shipment</span>
            </a>
            @endif
            <a href="{{ route('public.track') }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 shadow-sm transition">
                <i class="fa-solid fa-earth-americas text-blue-500 text-xs"></i>
                <span>Public Tracker</span>
            </a>
        </div>
    </div>

    <!-- 6 KPI Metric Cards (Soft Minimal SaaS Style) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Shipments -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</span>
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

    <!-- Active Exceptions Banner (Soft Card Style) -->
    @if(count($activeExceptions) > 0)
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

    <!-- Visual Charts (Clean White Cards with Soft Pastel Charts) -->
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

    <!-- Recent Shipments Table (Matching User Screenshot) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Recent Consignments</h3>
                <p class="text-[11px] text-slate-400">Latest active trade movements</p>
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
                            <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600" title="Inspect">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                            </a>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Doughnut Chart (Soft Calm Colors)
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

        // Bar Chart (Soft Blue Color)
        const carrierCtx = document.getElementById('carrierChart').getContext('2d');
        new Chart(carrierCtx, {
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
    });
</script>
@endpush
