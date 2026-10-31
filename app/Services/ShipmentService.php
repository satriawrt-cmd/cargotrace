<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ShipmentService
{
    public function __construct(
        protected EtaService $etaService,
        protected TrackingService $trackingService
    ) {}

    /**
     * Generate unique shipment number (e.g. CT-2026-A48F9).
     */
    public function generateShipmentNumber(): string
    {
        do {
            $number = 'CT-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (Shipment::where('shipment_number', $number)->exists());

        return $number;
    }

    /**
     * Create a new shipment with initial calculations and tracking milestone.
     */
    public function createShipment(array $data, ?User $actor = null): Shipment
    {
        return DB::transaction(function () use ($data, $actor) {
            $shipmentNumber = $this->generateShipmentNumber();

            $expectedDeparture = Carbon::parse($data['expected_departure']);
            $expectedArrival = Carbon::parse($data['expected_arrival']);
            $estimatedArrival = isset($data['estimated_arrival'])
                ? Carbon::parse($data['estimated_arrival'])
                : $expectedArrival->copy();

            // Evaluate ETA / delay
            $etaResult = $this->etaService->evaluateDelay($expectedArrival, $estimatedArrival);

            $origin = Location::findOrFail($data['origin_id']);

            $shippingCost = (float) ($data['shipping_cost'] ?? 0.00);
            $insuranceFee = (float) ($data['insurance_fee'] ?? 0.00);
            $declaredValue = (float) ($data['declared_value'] ?? 0.00);
            $totalCost = $shippingCost + $insuranceFee;

            $shipment = Shipment::create([
                'shipment_number' => $shipmentNumber,
                'customer_id' => $data['customer_id'],
                'carrier_id' => $data['carrier_id'],
                'origin_id' => $data['origin_id'],
                'destination_id' => $data['destination_id'],
                'cargo_type' => $data['cargo_type'],
                'weight_kg' => (float) ($data['weight_kg'] ?? 0),
                'volume_cbm' => (float) ($data['volume_cbm'] ?? 0),
                'expected_departure' => $expectedDeparture,
                'expected_arrival' => $expectedArrival,
                'estimated_arrival' => $estimatedArrival,
                'current_status' => Shipment::STATUS_CREATED,
                'delay_status' => $etaResult['delay_status'],
                'delay_hours' => $etaResult['delay_hours'],
                'delay_severity' => $etaResult['delay_severity'],
                'delay_reason' => $etaResult['delay_reason'],
                'current_location_name' => $origin->name . ' (' . $origin->city . ', ' . $origin->country . ')',
                'current_latitude' => $origin->latitude,
                'current_longitude' => $origin->longitude,
                'currency_code' => strtoupper($data['currency_code'] ?? 'USD'),
                'shipping_cost' => $shippingCost,
                'declared_value' => $declaredValue,
                'insurance_fee' => $insuranceFee,
                'total_cost' => $totalCost,
                'special_instructions' => $data['special_instructions'] ?? null,
            ]);

            // Save items if provided
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (!empty($item['item_description'])) {
                        ShipmentItem::create([
                            'shipment_id' => $shipment->id,
                            'item_description' => $item['item_description'],
                            'quantity' => (int) ($item['quantity'] ?? 1),
                            'package_type' => $item['package_type'] ?? 'Box',
                            'weight_kg' => (float) ($item['weight_kg'] ?? 0),
                            'declared_value' => (float) ($item['declared_value'] ?? 0),
                            'hs_code' => $item['hs_code'] ?? null,
                        ]);
                    }
                }
            }

            // Create baseline CREATED tracking checkpoint
            $this->trackingService->addEvent($shipment, [
                'status' => Shipment::STATUS_CREATED,
                'location_name' => $origin->name . ', ' . $origin->country,
                'latitude' => $origin->latitude,
                'longitude' => $origin->longitude,
                'event_time' => now(),
                'description' => "Shipment registered into CargoTrace visibility system.",
            ], $actor);

            // Audit log
            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'CREATE_SHIPMENT',
                'entity_type' => 'Shipment',
                'entity_id' => $shipment->id,
                'new_values' => [
                    'shipment_number' => $shipment->shipment_number,
                    'customer_id' => $shipment->customer_id,
                    'total_cost' => $shipment->total_cost,
                    'currency' => $shipment->currency_code,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $shipment;
        });
    }

    /**
     * Transition shipment status following strict business rules.
     */
    public function transitionStatus(Shipment $shipment, string $targetStatus, ?string $notes = null, ?User $actor = null): Shipment
    {
        return DB::transaction(function () use ($shipment, $targetStatus, $notes, $actor) {
            if (!$shipment->canTransitionTo($targetStatus)) {
                throw new InvalidArgumentException(
                    "Cannot transition shipment [{$shipment->shipment_number}] from [{$shipment->current_status}] to [{$targetStatus}]. Invalid status progression or terminal state."
                );
            }

            $oldStatus = $shipment->current_status;
            $updates = ['current_status' => $targetStatus];

            if ($targetStatus === Shipment::STATUS_IN_TRANSIT && !$shipment->actual_departure) {
                $updates['actual_departure'] = now();
            } elseif ($targetStatus === Shipment::STATUS_DELIVERED) {
                $updates['actual_arrival'] = now();
            }

            $shipment->update($updates);

            // Add corresponding tracking event
            $this->trackingService->addEvent($shipment, [
                'status' => $targetStatus,
                'location_name' => $shipment->current_location_name ?: ($shipment->origin->name ?? 'Hub'),
                'latitude' => $shipment->current_latitude,
                'longitude' => $shipment->current_longitude,
                'event_time' => now(),
                'description' => $notes ?: "Lifecycle status advanced from {$oldStatus} to {$targetStatus}.",
            ], $actor);

            // Audit log
            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'UPDATE_STATUS',
                'entity_type' => 'Shipment',
                'entity_id' => $shipment->id,
                'old_values' => ['current_status' => $oldStatus],
                'new_values' => ['current_status' => $targetStatus, 'notes' => $notes],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $shipment;
        });
    }

    /**
     * Update existing shipment information.
     */
    public function updateShipment(Shipment $shipment, array $data, ?User $actor = null): Shipment
    {
        return DB::transaction(function () use ($shipment, $data, $actor) {
            $oldValues = $shipment->only(['cargo_type', 'weight_kg', 'shipping_cost', 'expected_arrival', 'estimated_arrival']);

            $shippingCost = isset($data['shipping_cost']) ? (float) $data['shipping_cost'] : $shipment->shipping_cost;
            $insuranceFee = isset($data['insurance_fee']) ? (float) $data['insurance_fee'] : $shipment->insurance_fee;
            $data['total_cost'] = $shippingCost + $insuranceFee;

            if (!empty($data['expected_arrival']) || !empty($data['estimated_arrival'])) {
                $expected = !empty($data['expected_arrival']) ? Carbon::parse($data['expected_arrival']) : $shipment->expected_arrival;
                $estimated = !empty($data['estimated_arrival']) ? Carbon::parse($data['estimated_arrival']) : $shipment->estimated_arrival;
                $etaResult = $this->etaService->evaluateDelay($expected, $estimated);
                $data['delay_status'] = $etaResult['delay_status'];
                $data['delay_hours'] = $etaResult['delay_hours'];
                $data['delay_severity'] = $etaResult['delay_severity'];
                $data['delay_reason'] = $etaResult['delay_reason'];
            }

            $shipment->update($data);

            AuditLog::create([
                'user_id' => $actor?->id,
                'action' => 'UPDATE_SHIPMENT',
                'entity_type' => 'Shipment',
                'entity_id' => $shipment->id,
                'old_values' => $oldValues,
                'new_values' => $shipment->only(array_keys($oldValues)),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $shipment;
        });
    }
}
