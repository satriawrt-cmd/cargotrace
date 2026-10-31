<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ExceptionService
{
    public function __construct(
        protected TrackingService $trackingService,
        protected EtaService $etaService
    ) {}

    /**
     * Raise a new shipment exception.
     */
    public function createException(Shipment $shipment, array $data, ?User $actor = null): ShipmentException
    {
        return DB::transaction(function () use ($shipment, $data, $actor) {
            $detectedAt = isset($data['detected_at'])
                ? Carbon::parse($data['detected_at'])
                : now();

            // 1. Create a corresponding tracking event
            $trackingEvent = $this->trackingService->addEvent($shipment, [
                'status' => 'EXCEPTION',
                'location_name' => $data['location_name'] ?? ($shipment->current_location_name ?: $shipment->origin->name),
                'latitude' => $data['latitude'] ?? $shipment->current_latitude,
                'longitude' => $data['longitude'] ?? $shipment->current_longitude,
                'event_time' => $detectedAt,
                'description' => "[EXCEPTION: {$data['exception_type']}] " . $data['root_cause'],
                'is_exception' => true,
            ], $actor);

            // 2. Create the exception record
            $exception = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'tracking_event_id' => $trackingEvent->id,
                'exception_type' => $data['exception_type'],
                'severity' => $data['severity'] ?? 'MEDIUM',
                'status' => 'OPEN',
                'detected_at' => $detectedAt,
                'root_cause' => $data['root_cause'],
                'action_plan' => $data['action_plan'] ?? null,
                'reported_by_user_id' => $actor?->id,
            ]);

            // 3. If delay impact hours is specified, push ETA
            if (!empty($data['delay_impact_hours']) && (int) $data['delay_impact_hours'] > 0) {
                $newEstimated = $shipment->estimated_arrival->copy()->addHours((int) $data['delay_impact_hours']);
                $this->etaService->updateShipmentEta(
                    $shipment,
                    $newEstimated,
                    "Delay caused by exception: {$data['exception_type']}. {$data['root_cause']}"
                );
            }

            // 4. Notify Customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'type' => 'EXCEPTION_ALERT',
                'title' => "Incident Alert: {$shipment->shipment_number}",
                'message' => "Exception raised: [{$data['exception_type']}] - {$data['root_cause']}.",
                'action_url' => '/shipments/' . $shipment->id,
            ]);

            // 5. Audit Log
            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'RAISE_EXCEPTION',
                'entity_type' => 'ShipmentException',
                'entity_id' => $exception->id,
                'new_values' => [
                    'shipment_number' => $shipment->shipment_number,
                    'type' => $exception->exception_type,
                    'severity' => $exception->severity,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $exception;
        });
    }

    /**
     * Resolve an existing exception.
     */
    public function resolveException(ShipmentException $exception, array $data, ?User $actor = null): ShipmentException
    {
        return DB::transaction(function () use ($exception, $data, $actor) {
            if ($exception->isResolved()) {
                throw new InvalidArgumentException("This exception is already marked as resolved.");
            }

            $exception->update([
                'status' => 'RESOLVED',
                'resolved_at' => now(),
                'resolution_notes' => $data['resolution_notes'] ?? 'Resolved by operations team.',
                'resolved_by_user_id' => $actor?->id,
            ]);

            $shipment = $exception->shipment;

            // Add recovery checkpoint
            $this->trackingService->addEvent($shipment, [
                'status' => 'IN_TRANSIT',
                'location_name' => $shipment->current_location_name ?: 'En Route',
                'latitude' => $shipment->current_latitude,
                'longitude' => $shipment->current_longitude,
                'event_time' => now(),
                'description' => "[EXCEPTION RESOLVED: {$exception->exception_type}] " . ($data['resolution_notes'] ?? 'Issue cleared. Transit resumed.'),
                'is_exception' => false,
            ], $actor);

            // Audit log
            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'RESOLVE_EXCEPTION',
                'entity_type' => 'ShipmentException',
                'entity_id' => $exception->id,
                'new_values' => [
                    'status' => 'RESOLVED',
                    'resolution_notes' => $exception->resolution_notes,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $exception;
        });
    }
}
