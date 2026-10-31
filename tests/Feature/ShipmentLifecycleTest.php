<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ShipmentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_operator_can_create_shipment(): void
    {
        $operator = User::where('role', 'operator')->first();
        $customer = User::where('role', 'customer')->first();
        $carrier = Carrier::first();
        $origin = Location::first();
        $destination = Location::skip(1)->first();

        $response = $this->actingAs($operator)->post(route('shipments.store'), [
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $destination->id,
            'cargo_type' => 'High Grade Medical Diagnostic Devices',
            'weight_kg' => 4500,
            'volume_cbm' => 15.2,
            'expected_departure' => now()->format('Y-m-d H:i:s'),
            'expected_arrival' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'currency_code' => 'USD',
            'shipping_cost' => 3200,
            'declared_value' => 85000,
            'insurance_fee' => 255,
        ]);

        $this->assertDatabaseHas('shipments', [
            'cargo_type' => 'High Grade Medical Diagnostic Devices',
            'current_status' => Shipment::STATUS_CREATED,
        ]);

        $shipment = Shipment::where('cargo_type', 'High Grade Medical Diagnostic Devices')->first();
        $response->assertRedirect(route('shipments.show', $shipment->id));
    }

    public function test_status_transitions_follow_defined_business_rules(): void
    {
        $shipmentService = app(ShipmentService::class);
        $operator = User::where('role', 'operator')->first();

        $shipment = Shipment::where('current_status', Shipment::STATUS_CREATED)->first();
        if (!$shipment) {
            $shipment = Shipment::create([
                'shipment_number' => 'CT-TEST-001',
                'customer_id' => User::where('role', 'customer')->first()->id,
                'carrier_id' => Carrier::first()->id,
                'origin_id' => Location::first()->id,
                'destination_id' => Location::skip(1)->first()->id,
                'cargo_type' => 'Test Cargo',
                'expected_departure' => now(),
                'expected_arrival' => now()->addDays(5),
                'estimated_arrival' => now()->addDays(5),
                'current_status' => Shipment::STATUS_CREATED,
            ]);
        }

        // 1. CREATED -> BOOKED
        $shipment = $shipmentService->transitionStatus($shipment, Shipment::STATUS_BOOKED, null, $operator);
        $this->assertEquals(Shipment::STATUS_BOOKED, $shipment->current_status);

        // 2. BOOKED -> PICKED_UP
        $shipment = $shipmentService->transitionStatus($shipment, Shipment::STATUS_PICKED_UP, null, $operator);
        $this->assertEquals(Shipment::STATUS_PICKED_UP, $shipment->current_status);

        // 3. PICKED_UP -> IN_TRANSIT
        $shipment = $shipmentService->transitionStatus($shipment, Shipment::STATUS_IN_TRANSIT, null, $operator);
        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->current_status);
        $this->assertNotNull($shipment->actual_departure);

        // 4. IN_TRANSIT -> DELIVERED
        $shipment = $shipmentService->transitionStatus($shipment, Shipment::STATUS_DELIVERED, null, $operator);
        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->current_status);
        $this->assertNotNull($shipment->actual_arrival);
    }

    public function test_terminal_state_delivered_cannot_transition_back(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $shipmentService = app(ShipmentService::class);
        $operator = User::where('role', 'operator')->first();

        $deliveredShipment = Shipment::where('current_status', Shipment::STATUS_DELIVERED)->first();

        // Attempting to revert from DELIVERED to CREATED must throw InvalidArgumentException
        $shipmentService->transitionStatus($deliveredShipment, Shipment::STATUS_CREATED, null, $operator);
    }

    public function test_customer_can_only_view_own_shipment(): void
    {
        $customer1 = User::where('email', 'customer@globaltrade.test')->first();
        $customer2 = User::where('email', 'customer2@pacificbrands.test')->first();

        $shipmentOfCustomer2 = Shipment::where('customer_id', $customer2->id)->first();

        // Customer 1 accessing Customer 2's shipment should be forbidden (403)
        $response = $this->actingAs($customer1)->get(route('shipments.show', $shipmentOfCustomer2->id));
        $response->assertStatus(403);
    }
}
