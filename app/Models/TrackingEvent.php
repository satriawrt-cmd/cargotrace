<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'status',
        'location_name',
        'latitude',
        'longitude',
        'event_time',
        'description',
        'is_exception',
        'created_by_user_id',
        'weather_summary',
        'weather_temperature',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_exception' => 'boolean',
        'weather_temperature' => 'float',
        'event_time' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
