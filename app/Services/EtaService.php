<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Default delay threshold configuration (in hours).
     */
    protected int $lowThreshold = 6;
    protected int $mediumThreshold = 24;

    /**
     * Evaluate shipment delay by comparing expected and estimated arrival.
     *
     * @return array{
     *     delay_status: string,
     *     delay_hours: int,
     *     delay_severity: string,
     *     delay_reason: ?string
     * }
     */
    public function evaluateDelay(Carbon|string $expectedArrival, Carbon|string $estimatedArrival, ?string $reason = null): array
    {
        $expected = $expectedArrival instanceof Carbon ? $expectedArrival : Carbon::parse($expectedArrival);
        $estimated = $estimatedArrival instanceof Carbon ? $estimatedArrival : Carbon::parse($estimatedArrival);

        if ($estimated->lessThanOrEqualTo($expected)) {
            return [
                'delay_status' => 'ON_TIME',
                'delay_hours' => 0,
                'delay_severity' => 'NONE',
                'delay_reason' => null,
            ];
        }

        // Calculate positive delay hours
        $diffMinutes = $expected->diffInMinutes($estimated, false);
        $delayHours = (int) ceil(max(0, $diffMinutes) / 60);

        $severity = 'LOW';
        if ($delayHours > $this->mediumThreshold) {
            $severity = 'HIGH';
        } elseif ($delayHours > $this->lowThreshold) {
            $severity = 'MEDIUM';
        }

        $formattedReason = $reason ?: "ETA extended by {$delayHours} hours past expected arrival schedule.";

        return [
            'delay_status' => 'DELAYED',
            'delay_hours' => $delayHours,
            'delay_severity' => $severity,
            'delay_reason' => $formattedReason,
        ];
    }

    /**
     * Recalculate and update shipment delay attributes.
     */
    public function updateShipmentEta(Shipment $shipment, Carbon|string $newEstimatedArrival, ?string $reason = null): Shipment
    {
        $estimated = $newEstimatedArrival instanceof Carbon ? $newEstimatedArrival : Carbon::parse($newEstimatedArrival);
        $result = $this->evaluateDelay($shipment->expected_arrival, $estimated, $reason);

        $shipment->update([
            'estimated_arrival' => $estimated,
            'delay_status' => $result['delay_status'],
            'delay_hours' => $result['delay_hours'],
            'delay_severity' => $result['delay_severity'],
            'delay_reason' => $result['delay_reason'],
        ]);

        return $shipment;
    }
}
