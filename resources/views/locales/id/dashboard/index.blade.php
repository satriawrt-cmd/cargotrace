@extends('layouts.app')

@section('title', 'Dashboard Operasional')

@section('content')
<div class="space-y-6">
    <!-- Dashboard Header Hero Banner (Neutral Elegance Style) -->
    <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-br from-[#FFF9F3] via-[#FAF3EC] to-[#F5ECE2] border border-[#EBDCCF] shadow-sm relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-5">
        <!-- Soft decorative glow circle in corner -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#FFDBBB]/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-1.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#997E67]">Konsol Kontrol</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#FFDBBB]/70 text-[#664930] font-bold border border-[#F0CCAC] uppercase">
                    {{ Auth::check() ? (Auth::user()->role === 'admin' ? 'Administrator' : (Auth::user()->role === 'operator' ? 'Operator Logistik' : 'Pelanggan')) : 'Publik' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-[#3D2A1A]">
                @if(Auth::check() && Auth::user()->isCustomer())
                    Selamat Datang, {{ Auth::user()->company_name ?: Auth::user()->name }}
                @else
                    Dashboard Operasional
                @endif
            </h1>
            <p class="text-xs text-[#7A6350] mt-1 max-w-2xl leading-relaxed">
                @if(Auth::check() && Auth::user()->isCustomer())
                    Ringkasan konsinyasi kargo global terdaftar, jadwal tahapan pengiriman, dan pemesanan aktif Anda.
                @else
                    Ringkasan visibilitas pengiriman real-time, manajemen SLA insiden, dan penagihan multi-mata uang rute perdagangan dunia.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 relative z-10">
            @if(Auth::check() && Auth::user()->canManageShipments())
            <a href="{{ route('shipments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#664930] hover:bg-[#523A25] text-[#FFF8F2] font-semibold text-xs rounded-xl shadow-sm transition border border-[#523A25]">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Pengiriman</span>
            </a>
            @endif

            <a href="{{ route('public.track') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/90 hover:bg-white text-[#664930] font-semibold text-xs rounded-xl border border-[#E0D0C1] shadow-2xs transition">
                <i class="fa-solid fa-earth-americas text-[#997E67] text-xs"></i>
                <span>Buka Pelacak Langsung</span>
            </a>
        </div>
    </div>

    <!-- 6 KPI Metric Cards (Soft Aesthetic Palette) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Shipments -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-[#FFFDF9] to-[#FAF3EB] border border-[#EEDDCE] shadow-sm">
            <div class="flex items-center justify-between text-[#8C7664] mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#997E67]">{{ Auth::check() && Auth::user()->isCustomer() ? 'Kargo Saya' : 'Total Kargo' }}</span>
                <div class="w-7 h-7 rounded-lg bg-[#FFDBBB] text-[#664930] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-box"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#3D2A1A] font-mono">{{ $totalShipments }}</div>
            <span class="text-[11px] text-[#8C7664] mt-1 block">Konsinyasi aktif</span>
        </div>

        <!-- In Transit -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-[#FAF5EE] to-[#F3E8DC] border border-[#E5D5C4] shadow-sm">
            <div class="flex items-center justify-between text-[#72543B] mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#72543B]">Dalam Perjalanan</span>
                <div class="w-7 h-7 rounded-lg bg-[#997E67] text-[#FFF8F2] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-ship"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#72543B] font-mono">{{ $inTransitCount }}</div>
            <span class="text-[11px] text-[#8C7664] mt-1 block">Pelayaran berjalan</span>
        </div>

        <!-- Delivered -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-[#F4F8F4] to-[#E9F2E9] border border-[#D3E3D3] shadow-sm">
            <div class="flex items-center justify-between text-[#345937] mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#345937]">Terkirim</span>
                <div class="w-7 h-7 rounded-lg bg-[#DBEADB] text-[#345937] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#345937] font-mono">{{ $deliveredCount }}</div>
            <span class="text-[11px] text-[#6B856D] mt-1 block">Selesai & diserahkan</span>
        </div>

        <!-- Delayed -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-[#FFF5EC] to-[#FDEBD9] border border-[#F2DAC4] shadow-sm">
            <div class="flex items-center justify-between text-[#8C4626] mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#8C4626]">Terlambat</span>
                <div class="w-7 h-7 rounded-lg bg-[#FFDBBB] text-[#8C4626] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#8C4626] font-mono">{{ $delayedCount }}</div>
            <span class="text-[11px] text-[#A36C52] mt-1 block">Jadwal disesuaikan</span>
        </div>

        <!-- Open Exceptions -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-[#FDF3F1] to-[#FAE6E3] border border-[#F5D2CC] shadow-sm">
            <div class="flex items-center justify-between text-[#963E32] mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#963E32]">Insiden SLA</span>
                <div class="w-7 h-7 rounded-lg bg-[#F8D2CC] text-[#963E32] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#963E32] font-mono">{{ $openExceptionsCount }}</div>
            <span class="text-[11px] text-[#A35950] mt-1 block">Pengecualian aktif</span>
        </div>

        <!-- On-Time Rate KPI -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-[#FAF6F1] to-[#F2EAE0] border border-[#E6DACB] shadow-sm">
            <div class="flex items-center justify-between text-[#523E30] mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#523E30]">Ketepatan Waktu</span>
                <div class="w-7 h-7 rounded-lg bg-[#CCBEB1] text-[#523E30] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-[#3D2A1A] font-mono">{{ $onTimeRate }}%</div>
            <span class="text-[11px] text-[#8C7664] mt-1 block">Rata-rata: {{ $avgDelayHours }}j delay</span>
        </div>
    </div>

    <!-- Active Exceptions Banner (For Admin & Operator) -->
    @if((!Auth::check() || !Auth::user()->isCustomer()) && count($activeExceptions) > 0)
    <div class="p-4 rounded-2xl bg-[#FFF6ED] border border-[#F3DAC5] text-xs">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2 font-bold text-[#8C4626]">
                <i class="fa-solid fa-triangle-exclamation text-[#8C4626]"></i>
                <span>Insiden Aktif Memerlukan Tindak Lanjut SLA ({{ count($activeExceptions) }})</span>
            </div>
            <span class="text-[10px] text-[#A36C52] font-medium">Perhatian Segera</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
            @foreach($activeExceptions as $exc)
            <div class="p-3 bg-white/95 rounded-xl border border-[#EED4BF] flex items-center justify-between gap-3 shadow-2xs">
                <div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="font-bold text-[#3D2A1A] font-mono">{{ $exc->shipment->shipment_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#FDE7E3] text-[#963E32]">
                            {{ $exc->exception_type }}
                        </span>
                    </div>
                    <p class="text-[11px] text-[#7A6350] mt-0.5 line-clamp-1">{{ $exc->root_cause }}</p>
                </div>
                <a href="{{ route('shipments.show', $exc->shipment_id) }}" class="px-2.5 py-1 text-[11px] font-semibold text-[#8C4626] hover:text-[#664930] shrink-0">
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
        <div class="p-5 rounded-2xl bg-white/95 border border-[#EDE4DC] shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-[#443224] uppercase tracking-wider mb-4">Pengiriman Berdasarkan Status</h3>
                <div class="relative h-52 flex items-center justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-[#997E67] text-center mt-3">Distribusi siklus hidup pengiriman</p>
        </div>

        <!-- Carrier Bar Chart -->
        <div class="p-5 rounded-2xl bg-white/95 border border-[#EDE4DC] shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-[#443224] uppercase tracking-wider">Volume Berdasarkan Operator</h3>
                    <span class="text-[11px] text-[#997E67]">Total Muatan</span>
                </div>
                <div class="relative h-52">
                    <canvas id="carrierChart"></canvas>
                </div>
            </div>
            <p class="text-[11px] text-[#997E67] text-center mt-3">Pemesanan kontainer aktif per jalur pelayaran</p>
        </div>
    </div>
    @endif

    <!-- Recent Shipments Table (Neutral Elegance Style) -->
    <div class="bg-white/95 rounded-2xl border border-[#EDE4DC] shadow-sm overflow-hidden">
        <div class="p-4 border-b border-[#EDE4DC] bg-[#FAF5EE]/70 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#3D2A1A]">
                    {{ Auth::check() && Auth::user()->isCustomer() ? 'Daftar Konsinyasi Saya' : 'Konsinyasi Terbaru' }}
                </h3>
                <p class="text-[11px] text-[#997E67]">
                    {{ Auth::check() && Auth::user()->isCustomer() ? 'Pengiriman kargo dan jadwal kedatangan Anda' : 'Pergerakan perdagangan aktif terkini' }}
                </p>
            </div>
            <a href="{{ route('shipments.index') }}" class="text-xs font-semibold text-[#664930] hover:text-[#3D2A1A]">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-[#EDE4DC] bg-[#F8F2EA] text-[11px] font-semibold text-[#7A6451]">
                        <th class="py-3 px-5">Pengiriman</th>
                        <th class="py-3 px-4">Rute</th>
                        <th class="py-3 px-4">Operator</th>
                        <th class="py-3 px-4">Kargo</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Biaya ({{ $activeCurrencyCode }})</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2EAE2] text-[#6E5947]">
                    @forelse($recentShipments as $s)
                    <tr class="hover:bg-[#FAF5EE]/80 transition">
                        <td class="py-3.5 px-5 font-bold text-[#3D2A1A] font-mono">
                            <a href="{{ route('shipments.show', $s->id) }}" class="hover:text-[#664930]">
                                {{ $s->shipment_number }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-[#443224]">
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
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#EDF5EC] text-[#345937] border border-[#CCE2CB]">
                                    Terkirim
                                </span>
                            @elseif($s->current_status === 'IN_TRANSIT')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#F5EDE3] text-[#72543B] border border-[#E2D2C0]">
                                    Dalam Perjalanan
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF4ED] text-[#6E5947] border border-[#E8DCCF]">
                                    {{ $s->current_status === 'CREATED' ? 'Dibuat' : ($s->current_status === 'BOOKED' ? 'Terjadwal' : ($s->current_status === 'PICKED_UP' ? 'Diambil' : str_replace('_', ' ', $s->current_status))) }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-[#3D2A1A]">
                            {{ $currencyService->convertAndFormat($s->total_cost, $s->currency_code, $activeCurrencyCode) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('public.track', ['shipment_number' => $s->shipment_number]) }}"
                                   class="p-1.5 text-[#664930] hover:text-[#3D2A1A] bg-[#FFDBBB]/50 hover:bg-[#FFDBBB] rounded-lg transition" title="Lacak di Pelacak Langsung">
                                    <i class="fa-solid fa-satellite-dish text-xs"></i>
                                </a>
                                <a href="{{ route('shipments.show', $s->id) }}" class="p-1.5 text-[#8C7664] hover:text-[#3D2A1A] bg-[#F5EDE4] hover:bg-[#EADED1] rounded-lg transition" title="Rincian">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-[#997E67]">Belum ada pengiriman terdaftar.</td>
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
                        backgroundColor: ['#CCBEB1', '#FFDBBB', '#E2D2C2', '#997E67', '#664930'],
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
                            labels: { color: '#6E5947', font: { size: 10, family: 'Plus Jakarta Sans' }, padding: 8 }
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
                        backgroundColor: '#997E67',
                        borderRadius: 6,
                        hoverBackgroundColor: '#664930'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#997E67', stepSize: 1, font: { size: 10 } },
                            grid: { color: '#F4ECE1' }
                        },
                        x: {
                            ticks: { color: '#6E5947', font: { size: 10, family: 'Plus Jakarta Sans' } },
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