<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TrackingService
{
    public function __construct(
        protected ExternalWeatherService $weatherService
    ) {}

    /**
     * Add a tracking event to a shipment.
     */
    public function addEvent(
        Shipment $shipment,
        array $data,
        ?User $actor = null
    ): TrackingEvent {
        return DB::transaction(function () use ($shipment, $data, $actor) {
            $eventTime = isset($data['event_time'])
                ? Carbon::parse($data['event_time'])
                : now();

            // Validate that event timestamp is not unreasonably before latest existing event
            $latestEvent = $shipment->trackingEvents()->latest('event_time')->first();
            if ($latestEvent && $eventTime->lessThan($latestEvent->event_time->subHours(24))) {
                throw new InvalidArgumentException("Tracking event timestamp cannot precede earlier verified checkpoints by more than 24 hours.");
            }

            $latitude = $data['latitude'] ?? null;
            $longitude = $data['longitude'] ?? null;

            // Fetch external weather if coordinates are provided
            $weatherSummary = $data['weather_summary'] ?? null;
            $weatherTemp = $data['weather_temperature'] ?? null;

            if ($latitude !== null && $longitude !== null && empty($weatherSummary)) {
                $weather = $this->weatherService->getWeather((float) $latitude, (float) $longitude);
                $weatherSummary = $weather['summary'] . " ({$weather['temperature']}°C)";
                $weatherTemp = $weather['temperature'];
            }

            $event = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'status' => $data['status'],
                'location_name' => $data['location_name'],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'event_time' => $eventTime,
                'description' => $data['description'],
                'is_exception' => (bool) ($data['is_exception'] ?? false),
                'created_by_user_id' => $actor?->id,
                'weather_summary' => $weatherSummary,
                'weather_temperature' => $weatherTemp,
            ]);

            // Update shipment's current coordinates and location name
            $shipmentUpdates = [
                'current_location_name' => $data['location_name'],
            ];

            if ($latitude !== null && $longitude !== null) {
                $shipmentUpdates['current_latitude'] = $latitude;
                $shipmentUpdates['current_longitude'] = $longitude;
            }

            // Sync shipment status if the event represents a valid lifecycle milestone
            $newStatus = $data['shipment_status'] ?? $data['status'];
            if ($newStatus && in_array($newStatus, [
                Shipment::STATUS_BOOKED,
                Shipment::STATUS_PICKED_UP,
                Shipment::STATUS_IN_TRANSIT,
                Shipment::STATUS_DELIVERED
            ])) {
                if ($shipment->canTransitionTo($newStatus)) {
                    $shipmentUpdates['current_status'] = $newStatus;
                    if ($newStatus === Shipment::STATUS_DELIVERED) {
                        $shipmentUpdates['actual_arrival'] = $eventTime;
                    } elseif ($newStatus === Shipment::STATUS_IN_TRANSIT && !$shipment->actual_departure) {
                        $shipmentUpdates['actual_departure'] = $eventTime;
                    }
                }
            }

            $shipment->update($shipmentUpdates);

            // Notify Customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'type' => $event->is_exception ? 'EXCEPTION_ALERT' : 'STATUS_UPDATE',
                'title' => "Shipment Update: {$shipment->shipment_number}",
                'message' => "Checkpoint reached: {$data['location_name']}. Status: {$data['status']}.",
                'action_url' => '/shipments/' . $shipment->id,
            ]);

            // Audit Log
            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'ADD_TRACKING_EVENT',
                'entity_type' => 'Shipment',
                'entity_id' => $shipment->id,
                'old_values' => ['current_location' => $shipment->getOriginal('current_location_name')],
                'new_values' => [
                    'event_id' => $event->id,
                    'status' => $event->status,
                    'location' => $event->location_name,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $event;
        });
    }
}
