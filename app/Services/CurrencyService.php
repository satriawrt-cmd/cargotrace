<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    /**
     * Get all active currencies (cached for 1 hour).
     */
    public function getActiveCurrencies(): Collection
    {
        return Cache::remember('active_currencies', 3600, function () {
            return Currency::where('is_active', true)->orderBy('code')->get();
        });
    }

    /**
     * Get currency by 3-letter ISO code.
     */
    public function getCurrency(string $code): ?Currency
    {
        return Currency::where('code', strtoupper($code))->first();
    }

    /**
     * Convert an amount from one currency to another.
     */
    public function convert(float $amount, string $fromCode, string $toCode): float
    {
        $fromCode = strtoupper($fromCode);
        $toCode = strtoupper($toCode);

        if ($fromCode === $toCode) {
            return $amount;
        }

        $fromCurrency = $this->getCurrency($fromCode);
        $toCurrency = $this->getCurrency($toCode);

        if (!$fromCurrency || !$toCurrency) {
            return $amount;
        }

        // Convert from source to USD base
        $usdAmount = $amount / ($fromCurrency->exchange_rate_to_usd > 0 ? $fromCurrency->exchange_rate_to_usd : 1);

        // Convert from USD base to target
        $targetAmount = $usdAmount * $toCurrency->exchange_rate_to_usd;

        return round($targetAmount, 2);
    }

    /**
     * Format an amount in the specified currency.
     */
    public function format(float $amount, string $currencyCode): string
    {
        $currency = $this->getCurrency($currencyCode);
        $symbol = $currency ? $currency->symbol : $currencyCode . ' ';

        // Standard integer display for JPY / IDR without decimal places if desired, or 2 decimals
        if (in_array(strtoupper($currencyCode), ['IDR', 'JPY', 'KRW'])) {
            return $symbol . ' ' . number_format($amount, 0, ',', '.');
        }

        return $symbol . ' ' . number_format($amount, 2, '.', ',');
    }

    /**
     * Convert and format an amount into the active target currency.
     */
    public function convertAndFormat(float $amount, string $fromCurrencyCode, ?string $targetCurrencyCode = null): string
    {
        $targetCurrencyCode = $targetCurrencyCode ?: session('selected_currency', 'USD');
        $converted = $this->convert($amount, $fromCurrencyCode, $targetCurrencyCode);
        return $this->format($converted, $targetCurrencyCode);
    }

    /**
     * Update exchange rate for a currency.
     */
    public function updateExchangeRate(string $code, float $rate): bool
    {
        $currency = Currency::where('code', strtoupper($code))->first();
        if (!$currency) {
            return false;
        }

        $currency->update(['exchange_rate_to_usd' => $rate]);
        Cache::forget('active_currencies');
        return true;
    }
}
