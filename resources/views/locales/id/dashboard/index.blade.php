@extends('layouts.app')

@section('title', 'Dashboard Operasional')

@section('content')
<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Konsol Kontrol</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-bold border border-blue-200 uppercase">
                    {{ Auth::check() ? (Auth::user()->role === 'admin' ? 'Administrator' : (Auth::user()->role === 'operator' ? 'Operator Logistik' : 'Pelanggan')) : 'Publik' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 mt-0.5">
                @if(Auth::check() && Auth::user()->isCustomer())
                    Selamat Datang, {{ Auth::user()->company_name ?: Auth::user()->name }}
                @else
                    Dashboard Operasional
                @endif
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                @if(Auth::check() && Auth::user()->isCustomer())
                    Ringkasan konsinyasi kargo global terdaftar, jadwal tahapan pengiriman, dan pemesanan aktif Anda.
                @else
                    Ringkasan visibilitas pengiriman real-time, manajemen SLA insiden, dan penagihan multi-mata uang rute perdagangan dunia.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if(Auth::check() && Auth::user()->canManageShipments())
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Pengiriman</span>
            </a>
            @endif

            <a href="{{ route('public.track') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 shadow-xs transition">
                <i class="fa-solid fa-earth-americas text-blue-500 text-xs"></i>
                <span>Buka Pelacak Langsung</span>
            </a>
        </div>
    </div>

    <!-- 6 KPI Metric Cards (Soft Minimal SaaS Style) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Shipments -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ Auth::check() && Auth::user()->isCustomer() ? 'Kargo Saya' : 'Total Kargo' }}</span>
                <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-box"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ $totalShipments }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Konsinyasi aktif</span>
        </div>

        <!-- In Transit -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Dalam Perjalanan</span>
                <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-ship"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-blue-600 font-mono">{{ $inTransitCount }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Pelayaran berjalan</span>
        </div>

        <!-- Delivered -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Terkirim</span>
                <div class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-600 font-mono">{{ $deliveredCount }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Selesai & diserahkan</span>
        </div>

        <!-- Delayed -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Terlambat</span>
                <div class="w-6 h-6 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-600 font-mono">{{ $delayedCount }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Jadwal disesuaikan</span>
        </div>

        <!-- Open Exceptions -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-red-600">Insiden SLA</span>
                <div class="w-6 h-6 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-red-600 font-mono">{{ $openExceptionsCount }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Pengecualian aktif</span>
        </div>

        <!-- On-Time Rate KPI -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Ketepatan Waktu</span>
                <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ $onTimeRate }}%</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Rata-rata: {{ $avgDelayHours }}j delay</span>
        </div>
    </div>

    <!-- Active Exceptions Banner (For Admin & Operator) -->
    @if((!Auth::check() || !Auth::user()->isCustomer()) && count($activeExceptions) > 0)
    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2 font-bold text-amber-800">
                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                <span>Insiden Aktif Memerlukan Tindak Lanjut SLA ({{ count($activeExceptions) }})</span>
            </div>
            <span class="text-[10px] text-amber-700">Perhatian Segera</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
            @foreach($activeExceptions as $exc)
            <div class="p-3 bg-white rounded-xl border border-amber-200/70 flex items-center justify-between gap-3 shadow-xs">
                <div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="font-bold text-slate-900 font-mono">{{ $exc->shipment->shipment_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">
                            {{ $exc->exception_type }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-1">{{ $exc->root_cause }}</p>
                </div>
                <a href="{{ route('shipments.show', $exc->shipment_id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-blue-600 hover:text-blue-800 shrink-0">
                    Periksa &rarr;
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
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4">Pengiriman Berdasarkan Status</h3>
                <div class="relative h-52 flex items-center justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 text-center mt-3">Distribusi siklus hidup pengiriman</p>
        </div>

        <!-- Carrier Bar Chart -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Volume Berdasarkan Operator</h3>
                    <span class="text-[11px] text-slate-400">Total Muatan</span>
                </div>
                <div class="relative h-52">
                    <canvas id="carrierChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 text-center mt-3">Pemesanan kontainer aktif per jalur pelayaran</p>
        </div>
    </div>
    @endif

    <!-- Recent Shipments Table (Matching User Screenshot) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">
                    {{ Auth::check() && Auth::user()->isCustomer() ? 'Daftar Konsinyasi Saya' : 'Konsinyasi Terbaru' }}
                </h3>
                <p class="text-[11px] text-slate-400">
                    {{ Auth::check() && Auth::user()->isCustomer() ? 'Pengiriman kargo dan jadwal kedatangan Anda' : 'Pergerakan perdagangan aktif terkini' }}
                </p>
            </div>
            <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-semibold text-slate-500">
                        <th class="py-3 px-5">Pengiriman</th>
                        <th class="py-3 px-4">Rute</th>
                        <th class="py-3 px-4">Operator</th>
                        <th class="py-3 px-4">Kargo</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Biaya ({{ $activeCurrencyCode }})</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
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
                            {{ $s->localized_cargo_type }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($s->current_status === 'DELIVERED')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Terkirim
                                </span>
                            @elseif($s->current_status === 'IN_TRANSIT')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    Dalam Perjalanan
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $s->current_status === 'CREATED' ? 'Dibuat' : ($s->current_status === 'BOOKED' ? 'Terjadwal' : ($s->current_status === 'PICKED_UP' ? 'Diambil' : str_replace('_', ' ', $s->current_status))) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                            {{ $currencyService->convertAndFormat($s->total_cost, $s->currency_code, $activeCurrencyCode) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('public.track', ['shipment_number' => $s->shipment_number]) }}"
                                   class="p-1.5 text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition" title="Lacak di Pelacak Langsung">
                                    <i class="fa-solid fa-satellite-dish text-xs"></i>
                                </a>
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 text-slate-400 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-lg transition" title="Rincian">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada pengiriman terdaftar.</td>
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
        @if(!Auth::check() || !Auth::user()->isCustomer())
        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            new Chart(statusCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Dibuat', 'Terjadwal', 'Kargo Diambil', 'Dalam Perjalanan', 'Terkirim'],
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
                        label: 'Jumlah Kargo',
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
