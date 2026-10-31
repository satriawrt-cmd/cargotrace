<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate_to_usd',
        'is_active',
    ];

    protected $casts = [
        'exchange_rate_to_usd' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Convert an amount from USD to this currency.
     */
    public function fromUsd(float $amountInUsd): float
    {
        return round($amountInUsd * $this->exchange_rate_to_usd, 2);
    }

    /**
     * Convert an amount from this currency to USD.
     */
    public function toUsd(float $amountInThisCurrency): float
    {
        if ($this->exchange_rate_to_usd <= 0) {
            return $amountInThisCurrency;
        }
        return round($amountInThisCurrency / $this->exchange_rate_to_usd, 2);
    }
}
