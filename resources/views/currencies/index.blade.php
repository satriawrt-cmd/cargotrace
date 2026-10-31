@extends('layouts.app')

@section('title', 'Global Currencies & Exchange Rates')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3">
                <i class="fa-solid fa-coins text-amber-400"></i>
                <span>World Currencies & Multi-Currency Engine</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Dynamically switch and calculate transaction values across global currencies: USD, EUR, IDR, GBP, JPY, SGD, AUD, and more.
            </p>
        </div>

        <div class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 text-xs flex items-center gap-2">
            <span class="text-slate-400">Active Global Currency:</span>
            <span class="font-bold text-amber-400 font-mono text-sm px-2 py-0.5 rounded bg-amber-500/10 border border-amber-500/20">
                {{ $activeCurrencyCode }}
            </span>
        </div>
    </div>

    <!-- Live Interactive Global Currency Converter Widget -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900 to-amber-950/30 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-calculator text-amber-400"></i>
                <span>Instant Multi-Currency Freight & Value Converter</span>
            </h3>
            <span class="text-[10px] text-slate-400 font-mono">Real-time Cross Rate Calculation</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-center">
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Amount</label>
                <input type="number" id="convertAmount" value="5000" min="0" step="any"
                       class="w-full px-3 py-2 text-sm bg-slate-950 border border-slate-800 rounded-xl text-white font-mono focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">From Currency</label>
                <select id="convertFrom" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-400 font-bold">
                    @foreach($currencies as $c)
                        <option value="{{ $c->code }}" data-rate="{{ $c->exchange_rate_to_usd }}" {{ $c->code === 'USD' ? 'selected' : '' }}>
                            {{ $c->code }} ({{ $c->symbol }}) - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">To Currency</label>
                <select id="convertTo" class="w-full px-3 py-2 text-xs bg-slate-950 border border-slate-800 rounded-xl text-white focus:outline-none focus:border-amber-400 font-bold">
                    @foreach($currencies as $c)
                        <option value="{{ $c->code }}" data-rate="{{ $c->exchange_rate_to_usd }}" {{ $c->code === 'IDR' ? 'selected' : '' }}>
                            {{ $c->code }} ({{ $c->symbol }}) - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <span class="block text-xs font-semibold text-slate-400 mb-1">Converted Value</span>
                <div id="convertedResult" class="px-3 py-2 text-base font-extrabold text-amber-300 font-mono bg-slate-950/80 rounded-xl border border-amber-500/30">
                    --
                </div>
            </div>
        </div>
    </div>

    <!-- Currencies Rates Registry Table -->
    <div class="rounded-3xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white">Active Global Currency Rates Table</h3>
            <span class="text-xs text-slate-500 font-mono">Base Reference: 1.00 USD</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 text-slate-400 uppercase text-[10px] tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Currency Name</th>
                        <th class="py-3 px-4">Symbol</th>
                        <th class="py-3 px-4 text-right">Exchange Rate (per 1 USD)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Set Display</th>
                        @if(Auth::user()->isAdmin())
                        <th class="py-3 px-4 text-right">Admin Rate Control</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @foreach($currencies as $curr)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-white text-sm">
                            {{ $curr->code }}
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-200">
                            {{ $curr->name }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-amber-400 text-sm">
                            {{ $curr->symbol }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                            {{ number_format($curr->exchange_rate_to_usd, 4) }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400">
                                Active
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <form action="{{ route('currency.switch') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="currency_code" value="{{ $curr->code }}">
                                <button type="submit" class="px-3 py-1 rounded-lg text-xs font-semibold transition
                                    {{ ($activeCurrencyCode ?? 'USD') === $curr->code ? 'bg-amber-500 text-slate-950 shadow-md font-bold' : 'bg-slate-800 hover:bg-slate-700 text-slate-300' }}">
                                    {{ ($activeCurrencyCode ?? 'USD') === $curr->code ? 'Active' : 'Select' }}
                                </button>
                            </form>
                        </td>
                        @if(Auth::user()->isAdmin())
                        <td class="py-3.5 px-4 text-right">
                            <form action="{{ route('currencies.update-rate', $curr->id) }}" method="POST" class="inline-flex items-center gap-1 justify-end">
                                @csrf
                                @method('PATCH')
                                <input type="number" step="0.0001" name="exchange_rate_to_usd" value="{{ $curr->exchange_rate_to_usd }}" class="w-24 px-2 py-1 text-xs bg-slate-950 border border-slate-700 rounded-lg text-white font-mono text-right">
                                <button type="submit" class="p-1 px-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs" title="Update">
                                    <i class="fa-solid fa-check"></i>
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

            // USD base = amount / fromRate
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
