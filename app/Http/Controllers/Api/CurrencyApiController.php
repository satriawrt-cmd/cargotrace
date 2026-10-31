<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyApiController extends Controller
{
    public function __construct(
        protected CurrencyService $currencyService
    ) {}

    public function index(): JsonResponse
    {
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();
        return response()->json(['data' => $currencies], 200);
    }

    public function convert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'from' => ['required', 'string', 'size:3', 'exists:currencies,code'],
            'to' => ['required', 'string', 'size:3', 'exists:currencies,code'],
        ]);

        $amount = (float) $validated['amount'];
        $from = strtoupper($validated['from']);
        $to = strtoupper($validated['to']);

        $converted = $this->currencyService->convert($amount, $from, $to);
        $formatted = $this->currencyService->format($converted, $to);

        return response()->json([
            'original_amount' => $amount,
            'from_currency' => $from,
            'to_currency' => $to,
            'converted_amount' => $converted,
            'formatted' => $formatted,
        ], 200);
    }
}
