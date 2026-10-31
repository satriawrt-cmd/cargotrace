<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'city',
        'country',
        'continent',
        'type',
        'latitude',
        'longitude',
        'timezone',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function originShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_id');
    }

    public function destinationShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_id');
    }

    public function getFormattedCoordinatesAttribute(): string
    {
        return sprintf('%.4f, %.4f', $this->latitude, $this->longitude);
    }
}
