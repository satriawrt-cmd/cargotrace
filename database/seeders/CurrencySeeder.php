<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate_to_usd' => 1.000000],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate_to_usd' => 0.923400],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate_to_usd' => 0.791200],
            ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'exchange_rate_to_usd' => 15850.000000],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate_to_usd' => 153.450000],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'exchange_rate_to_usd' => 1.324000],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate_to_usd' => 1.521000],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate_to_usd' => 1.389000],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'exchange_rate_to_usd' => 7.238000],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'AED', 'exchange_rate_to_usd' => 3.672500],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'exchange_rate_to_usd' => 0.884500],
        ];

        foreach ($currencies as $curr) {
            Currency::updateOrCreate(['code' => $curr['code']], $curr);
        }
    }
}
