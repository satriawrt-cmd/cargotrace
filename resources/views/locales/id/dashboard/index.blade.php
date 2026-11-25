@extends('layouts.app')

@section('title', 'Dashboard Operasional')

@section('content')
<div class="space-y-6">
    <!-- Dashboard Header Hero Banner (Ocean Maritime Style) -->
    <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-br from-[#0B132B] via-[#163B65] to-[#1E3A8A] border border-[#1E3A8A] shadow-lg relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-5 text-white">
        <!-- Soft decorative glow circle in corner -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#38BDF8]/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-300">Konsol Kontrol</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#1E293B] text-sky-300 font-bold border border-sky-400/30 uppercase">
                    {{ Auth::check() ? (Auth::user()->role === 'admin' ? 'Administrator' : (Auth::user()->role === 'operator' ? 'Operator Logistik' : 'Pelanggan')) : 'Publik' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
                @if(Auth::check() && Auth::user()->isCustomer())
                    Selamat Datang, {{ Auth::user()->company_name ?: Auth::user()->name }}
                @else
                    Dashboard Operasional Maritim
                @endif
            </h1>
            <p class="text-xs text-sky-100/90 mt-1 max-w-2xl leading-relaxed">
                @if(Auth::check() && Auth::user()->isCustomer())
                    Ringkasan konsinyasi kargo global terdaftar, jadwal tahapan pengiriman, dan pemesanan aktif Anda.
                @else
                    Ringkasan visibilitas pengiriman real-time, manajemen SLA insiden, dan penagihan multi-mata uang rute perdagangan dunia.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 relative z-10">
            @if(Auth::check() && Auth::user()->canManageShipments())
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#38BDF8] hover:bg-[#7DD3FC] text-[#082F49] font-bold text-xs rounded-xl shadow-sm transition border border-sky-400">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Pengiriman</span>
            </a>
            @endif

            <a href="{{ route('public.track') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 shadow-xs transition">
                <i class="fa-solid fa-earth-americas text-sky-300 text-xs"></i>
                <span>Buka Pelacak Langsung</span>
            </a>
        </div>
    </div>

    <!-- 6 KPI Metric Cards (Crisp Clean Ocean Maritime Palette) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Shipments -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-[#1E3A8A] transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ Auth::check() && Auth::user()->isCustomer() ? 'Kargo Saya' : 'Total Kargo' }}</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#1E3A8A] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-box"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#0F172A] font-mono">{{ $totalShipments }}</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Konsinyasi aktif</span>
        </div>

        <!-- In Transit -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-[#0284C7] transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#0284C7]">Dalam Perjalanan</span>
                <div class="w-7 h-7 rounded-lg bg-sky-50 text-[#0284C7] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-ship"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#0284C7] font-mono">{{ $inTransitCount }}</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Pelayaran berjalan</span>
        </div>

        <!-- Delivered -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-emerald-500 transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Terkirim</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-600 font-mono">{{ $deliveredCount }}</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Selesai & diserahkan</span>
        </div>

        <!-- Delayed -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-amber-500 transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Terlambat</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-600 font-mono">{{ $delayedCount }}</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Jadwal disesuaikan</span>
        </div>

        <!-- Open Exceptions -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-rose-500 transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600">Insiden SLA</span>
                <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-rose-600 font-mono">{{ $openExceptionsCount }}</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Pengecualian aktif</span>
        </div>

        <!-- On-Time Rate KPI -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-indigo-500 transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Ketepatan Waktu</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#0F172A] font-mono">{{ $onTimeRate }}%</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Rata-rata: {{ $avgDelayHours }}j delay</span>
        </div>
    </div>

    <!-- Active Exceptions Banner (For Admin & Operator) -->
    @if((!Auth::check() || !Auth::user()->isCustomer()) && count($activeExceptions) > 0)
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2 font-bold text-amber-900">
                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                <span>Insiden Aktif Memerlukan Tindak Lanjut SLA ({{ count($activeExceptions) }})</span>
            </div>
            <span class="text-[10px] text-amber-700 font-medium">Perhatian Segera</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
            @foreach($activeExceptions as $exc)
            <div class="p-3 bg-white rounded-xl border border-amber-200 flex items-center justify-between gap-3 shadow-2xs">
                <div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="font-bold text-[#0F172A] font-mono">{{ $exc->shipment->shipment_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800">
                            {{ $exc->exception_type }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-1">{{ $exc->root_cause }}</p>
                </div>
                <a href="{{ route('shipments.show', $exc->shipment_id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-[#1E3A8A] hover:text-[#0B132B] shrink-0">
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
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-4">Pengiriman Berdasarkan Status</h3>
                <div class="relative h-52 flex items-center justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 text-center mt-3">Distribusi siklus hidup pengiriman</p>
        </div>

        <!-- Carrier Bar Chart -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Volume Berdasarkan Operator</h3>
                    <span class="text-[11px] text-slate-500">Total Muatan</span>
                </div>
                <div class="relative h-52">
                    <canvas id="carrierChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 text-center mt-3">Pemesanan kontainer aktif per jalur pelayaran</p>
        </div>
    </div>
    @endif

    <!-- Recent Shipments Table (Ocean Maritime Style) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#0F172A]">
                    {{ Auth::check() && Auth::user()->isCustomer() ? 'Daftar Konsinyasi Saya' : 'Konsinyasi Terbaru' }}
                </h3>
                <p class="text-[11px] text-slate-500">
                    {{ Auth::check() && Auth::user()->isCustomer() ? 'Pengiriman kargo dan jadwal kedatangan Anda' : 'Pergerakan perdagangan aktif terkini' }}
                </p>
            </div>
            <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-[#1E3A8A] hover:text-[#0B132B]">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-100/70 text-[11px] font-semibold text-slate-700">
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
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3.5 px-5 font-bold text-[#0F172A] font-mono">
                            <a href="{{ route('shipments.show', $s->id) }}" class="hover:text-[#1E3A8A]">
                                {{ $s->shipment_number }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-800">
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
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Terkirim
                                </span>
                            @elseif($s->current_status === 'IN_TRANSIT')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                    Dalam Perjalanan
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $s->current_status === 'CREATED' ? 'Dibuat' : ($s->current_status === 'BOOKED' ? 'Terjadwal' : ($s->current_status === 'PICKED_UP' ? 'Diambil' : str_replace('_', ' ', $s->current_status))) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-[#0F172A]">
                            {{ $currencyService->convertAndFormat($s->total_cost, $s->currency_code, $activeCurrencyCode) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('public.track', ['shipment_number' => $s->shipment_number]) }}"
                                   class="p-1.5 text-[#1E3A8A] hover:text-[#0B132B] bg-sky-50 hover:bg-sky-100 rounded-lg transition" title="Lacak di Pelacak Langsung">
                                    <i class="fa-solid fa-satellite-dish text-xs"></i>
                                </a>
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 text-slate-600 hover:text-[#0F172A] bg-slate-100 hover:bg-slate-200 rounded-lg transition" title="Rincian">
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
                        backgroundColor: ['#94A3B8', '#38BDF8', '#2563EB', '#1E3A8A', '#10B981'],
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
                            labels: { color: '#475569', font: { size: 10, family: 'Plus Jakarta Sans' }, padding: 8 }
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
                        backgroundColor: '#1E3A8A',
                        borderRadius: 6,
                        hoverBackgroundColor: '#38BDF8'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#64748B', stepSize: 1, font: { size: 10 } },
                            grid: { color: '#F1F5F9' }
                        },
                        x: {
                            ticks: { color: '#475569', font: { size: 10, family: 'Plus Jakarta Sans' } },
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