@extends('layouts.app')

@section('title', 'Kurs Mata Uang')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Mata Uang Dunia & Nilai Tukar Kurs</h1>
            <p class="text-xs text-slate-500 mt-0.5">Mata uang transaksi global: USD, EUR, IDR, GBP, JPY, SGD, AUD, dan lainnya.</p>
        </div>

        <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-xs flex items-center gap-2 shadow-xs">
            <span class="text-slate-400">Tampilan Aktif:</span>
            <span class="font-bold text-blue-600 font-mono text-sm px-2 py-0.5 rounded bg-blue-50 border border-blue-200">
                {{ $activeCurrencyCode }}
            </span>
        </div>
    </div>

    <!-- Live Interactive Converter Widget (Soft Minimal SaaS Card) -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-calculator text-amber-500"></i>
                <span>Kalkulator Nilai & Biaya Kirim Lintas Mata Uang</span>
            </h3>
            <span class="text-[10px] text-slate-400 font-mono">Perhitungan Multi-Mata Uang Real-Time</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-center">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nominal</label>
                <input type="number" id="convertAmount" value="5000" min="0" step="any"
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono focus:outline-none focus:bg-white focus:border-blue-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Mata Uang</label>
                <select id="convertFrom" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-semibold focus:outline-none focus:bg-white focus:border-blue-500">
                    @foreach($currencies as $c)
                        <option value="{{ $c->code }}" data-rate="{{ $c->exchange_rate_to_usd }}" {{ $c->code === 'USD' ? 'selected' : '' }}>
                            {{ $c->code }} ({{ $c->symbol }}) - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ke Mata Uang</label>
                <select id="convertTo" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-semibold focus:outline-none focus:bg-white focus:border-blue-500">
                    @foreach($currencies as $c)
                        <option value="{{ $c->code }}" data-rate="{{ $c->exchange_rate_to_usd }}" {{ $c->code === 'IDR' ? 'selected' : '' }}>
                            {{ $c->code }} ({{ $c->symbol }}) - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <span class="block text-[11px] font-semibold text-slate-600 mb-1">Hasil Konversi</span>
                <div id="convertedResult" class="px-3 py-2 text-sm font-bold text-slate-900 font-mono bg-slate-50 rounded-xl border border-slate-200">
                    --
                </div>
            </div>
        </div>
    </div>

    <!-- Currencies Rates Table (Soft White Style) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Tabel Nilai Tukar Kurs</h3>
            <span class="text-[11px] text-slate-400 font-mono">Basis: 1.00 USD</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-semibold text-slate-500">
                        <th class="py-3 px-5">Kode</th>
                        <th class="py-3 px-4">Nama Mata Uang</th>
                        <th class="py-3 px-4">Simbol</th>
                        <th class="py-3 px-4 text-right">Nilai Tukar (per 1 USD)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Pilihan Aktif</th>
                        @if(Auth::user()->isAdmin())
                        <th class="py-3 px-4 text-right">Kontrol Kurs Admin</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @foreach($currencies as $curr)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-5 font-mono font-bold text-slate-900">
                            {{ $curr->code }}
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-800">
                            {{ $curr->name }}
                        </td>
                        <td class="py-3 px-4 font-bold text-amber-600">
                            {{ $curr->symbol }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-semibold text-slate-900">
                            {{ number_format($curr->exchange_rate_to_usd, 4) }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Aktif
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <form action="{{ route('currency.switch') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="currency_code" value="{{ $curr->code }}">
                                <button type="submit" class="px-2.5 py-0.5 rounded-lg text-xs font-semibold transition
                                    {{ ($activeCurrencyCode ?? 'USD') === $curr->code ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-600' }}">
                                    {{ ($activeCurrencyCode ?? 'USD') === $curr->code ? 'Aktif' : 'Pilih' }}
                                </button>
                            </form>
                        </td>
                        @if(Auth::user()->isAdmin())
                        <td class="py-3 px-4 text-right">
                            <form action="{{ route('currencies.update-rate', $curr->id) }}" method="POST" class="inline-flex items-center gap-1 justify-end">
                                @csrf
                                @method('PATCH')
                                <input type="number" step="0.0001" name="exchange_rate_to_usd" value="{{ $curr->exchange_rate_to_usd }}" class="w-24 px-2 py-1 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-800 font-mono text-right">
                                <button type="submit" class="p-1 px-2 rounded-lg bg-slate-800 text-white text-xs" title="Simpan Kurs">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountEl = document.getElementById('convertAmount');
        const fromEl = document.getElementById('convertFrom');
        const toEl = document.getElementById('convertTo');
        const resultEl = document.getElementById('convertedResult');

        function recalculate() {
            const amount = parseFloat(amountEl.value) || 0;
            const fromRate = parseFloat(fromEl.selectedOptions[0].getAttribute('data-rate')) || 1;
            const toRate = parseFloat(toEl.selectedOptions[0].getAttribute('data-rate')) || 1;
            const toCode = toEl.value;

            const usdBase = amount / fromRate;
            const targetVal = usdBase * toRate;

            let formatted = targetVal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            if (['IDR', 'JPY'].includes(toCode)) {
                formatted = Math.round(targetVal).toLocaleString('id-ID');
            }

            resultEl.textContent = `${toCode} ${formatted}`;
        }

        amountEl.addEventListener('input', recalculate);
        fromEl.addEventListener('change', recalculate);
        toEl.addEventListener('change', recalculate);

        recalculate();
    });
</script>
@endpush
