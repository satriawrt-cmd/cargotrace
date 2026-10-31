<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentException extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'tracking_event_id',
        'exception_type',
        'severity',
        'status',
        'detected_at',
        'resolved_at',
        'root_cause',
        'action_plan',
        'resolution_notes',
        'reported_by_user_id',
        'resolved_by_user_id',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function trackingEvent(): BelongsTo
    {
        return $this->belongsTo(TrackingEvent::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['OPEN', 'INVESTIGATING']);
    }

    public function isResolved(): bool
    {
        return $this->status === 'RESOLVED';
    }
}
