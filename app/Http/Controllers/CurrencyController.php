<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    public function index()
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Administrator access required.');
        }

        $currencies = Currency::orderBy('code')->get();
        return view('currencies.index', compact('currencies'));
    }

    public function switchCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency_code' => ['required', 'string', 'size:3', 'exists:currencies,code'],
        ]);

        $code = strtoupper($validated['currency_code']);
        session(['selected_currency' => $code]);

        if (Auth::check()) {
            Auth::user()->update(['preferred_currency' => $code]);
        }

        return back()->with('success', "Active display currency changed to {$code}.");
    }

    public function updateRate(Request $request, Currency $currency)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'exchange_rate_to_usd' => ['required', 'numeric', 'min:0.000001'],
        ]);

        $oldRate = $currency->exchange_rate_to_usd;
        $currency->update([
            'exchange_rate_to_usd' => $validated['exchange_rate_to_usd'],
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE_CURRENCY_RATE',
            'entity_type' => 'Currency',
            'entity_id' => $currency->id,
            'old_values' => ['rate' => $oldRate],
            'new_values' => ['rate' => $currency->exchange_rate_to_usd],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Exchange rate for {$currency->code} updated to {$currency->exchange_rate_to_usd}.");
    }
}
