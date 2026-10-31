<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    public const STATUS_CREATED = 'CREATED';
    public const STATUS_BOOKED = 'BOOKED';
    public const STATUS_PICKED_UP = 'PICKED_UP';
    public const STATUS_IN_TRANSIT = 'IN_TRANSIT';
    public const STATUS_DELIVERED = 'DELIVERED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const VALID_TRANSITIONS = [
        self::STATUS_CREATED => [self::STATUS_BOOKED, self::STATUS_CANCELLED],
        self::STATUS_BOOKED => [self::STATUS_PICKED_UP, self::STATUS_CANCELLED],
        self::STATUS_PICKED_UP => [self::STATUS_IN_TRANSIT, self::STATUS_CANCELLED],
        self::STATUS_IN_TRANSIT => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
        self::STATUS_DELIVERED => [], // Terminal state!
        self::STATUS_CANCELLED => [], // Terminal state!
    ];

    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_id',
        'destination_id',
        'cargo_type',
        'weight_kg',
        'volume_cbm',
        'expected_departure',
        'actual_departure',
        'expected_arrival',
        'estimated_arrival',
        'actual_arrival',
        'current_status',
        'delay_status',
        'delay_hours',
        'delay_severity',
        'delay_reason',
        'current_location_name',
        'current_latitude',
        'current_longitude',
        'currency_code',
        'shipping_cost',
        'declared_value',
        'insurance_fee',
        'total_cost',
        'special_instructions',
    ];

    protected $casts = [
        'weight_kg' => 'float',
        'volume_cbm' => 'float',
        'shipping_cost' => 'float',
        'declared_value' => 'float',
        'insurance_fee' => 'float',
        'total_cost' => 'float',
        'current_latitude' => 'float',
        'current_longitude' => 'float',
        'delay_hours' => 'integer',
        'expected_departure' => 'datetime',
        'actual_departure' => 'datetime',
        'expected_arrival' => 'datetime',
        'estimated_arrival' => 'datetime',
        'actual_arrival' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc');
    }

    public function latestTrackingEvent()
    {
        return $this->hasOne(TrackingEvent::class)->latestOfMany('event_time');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->latest('detected_at');
    }

    public function openExceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->whereIn('status', ['OPEN', 'INVESTIGATING']);
    }

    public function isTerminal(): bool
    {
        return in_array($this->current_status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    public function canTransitionTo(string $targetStatus): bool
    {
        if ($this->isTerminal()) {
            return false;
        }

        $allowed = self::VALID_TRANSITIONS[$this->current_status] ?? [];
        return in_array($targetStatus, $allowed, true);
    }

    /**
     * Scope shipments for the current user based on role.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return $query;
        }

        return $query->where('customer_id', $user->id);
    }

    /**
     * Filter shipments by query parameters.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                  ->orWhere('cargo_type', 'like', "%{$search}%")
                  ->orWhereHas('carrier', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('origin', function ($oq) use ($search) {
                      $oq->where('city', 'like', "%{$search}%")
                         ->orWhere('country', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('destination', function ($dq) use ($search) {
                      $dq->where('city', 'like', "%{$search}%")
                         ->orWhere('country', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['status'])) {
            $query->where('current_status', $filters['status']);
        }

        if (!empty($filters['carrier_id'])) {
            $query->where('carrier_id', $filters['carrier_id']);
        }

        if (!empty($filters['origin_id'])) {
            $query->where('origin_id', $filters['origin_id']);
        }

        if (!empty($filters['destination_id'])) {
            $query->where('destination_id', $filters['destination_id']);
        }

        if (!empty($filters['delay_status'])) {
            $query->where('delay_status', $filters['delay_status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('expected_departure', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('expected_arrival', '<=', $filters['date_to']);
        }

        return $query;
    }
}
