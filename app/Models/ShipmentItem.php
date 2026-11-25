<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'item_description',
        'quantity',
        'package_type',
        'weight_kg',
        'declared_value',
        'hs_code',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'weight_kg' => 'float',
        'declared_value' => 'float',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function getLocalizedDescriptionAttribute(): string
    {
        return \App\Services\TranslationHelper::translateText($this->item_description);
    }

    public function getLocalizedPackageTypeAttribute(): string
    {
        return \App\Services\TranslationHelper::translateText($this->package_type);
    }
}
