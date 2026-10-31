<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RestApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_api_shipments_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/shipments');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_shipments_via_api(): void
    {
        $user = User::where('role', 'operator')->first();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/shipments');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'shipment_number',
                        'current_status',
                        'delay_status',
                        'currency_code',
                        'total_cost',
                    ]
                ],
                'current_page',
                'per_page',
                'total',
            ]);
    }

    public function test_operator_can_add_tracking_event_via_api(): void
    {
        $operator = User::where('role', 'operator')->first();
        Sanctum::actingAs($operator);

        $shipment = Shipment::where('current_status', Shipment::STATUS_IN_TRANSIT)->first();

        $response = $this->postJson("/api/shipments/{$shipment->id}/tracking", [
            'status' => 'IN_TRANSIT',
            'location_name' => 'Bay of Bengal Maritime Corridor',
            'latitude' => 12.5000,
            'longitude' => 88.2000,
            'description' => 'Container vessel moving at 19.4 knots, engines nominal.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('event.location_name', 'Bay of Bengal Maritime Corridor');

        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'location_name' => 'Bay of Bengal Maritime Corridor',
        ]);
    }

    public function test_api_public_carriers_and_locations(): void
    {
        $user = User::first();
        Sanctum::actingAs($user);

        $responseCarriers = $this->getJson('/api/carriers');
        $responseCarriers->assertStatus(200);

        $responseLocations = $this->getJson('/api/locations');
        $responseLocations->assertStatus(200);
    }
}
