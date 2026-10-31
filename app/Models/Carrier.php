<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'contact_email',
        'contact_phone',
        'service_type',
        'tracking_url_template',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
