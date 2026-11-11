<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Currency;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementAndRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $operator;
    protected User $customer1;
    protected User $customer2;
    protected Carrier $carrier;
    protected Location $origin;
    protected Location $destination;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->first();
        $this->operator = User::where('role', 'operator')->first();
        $this->customer1 = User::where('role', 'customer')->first();
        $this->customer2 = User::where('email', 'customer2@pacificbrands.test')->first()
            ?? User::factory()->create(['role' => 'customer', 'email' => 'other@customer.test']);

        $this->carrier = Carrier::first();
        $this->origin = Location::where('code', 'SGSIN')->first() ?? Location::first();
        $this->destination = Location::where('code', 'NLRTM')->first() ?? Location::skip(1)->first();
    }

    public function test_admin_can_manage_users_crud(): void
    {
        $this->actingAs($this->admin);

        // 1. List users
        $response = $this->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('User Management');

        // 2. Create new user
        $storeResponse = $this->post(route('users.store'), [
            'name' => 'Test Operator Account',
            'email' => 'newoperator@cargotrace.test',
            'password' => 'secret12345',
            'role' => 'operator',
            'company_name' => 'CargoTrace Operations Hub',
            'phone' => '+628111222333',
            'preferred_currency' => 'USD',
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'newoperator@cargotrace.test', 'role' => 'operator']);

        $createdUser = User::where('email', 'newoperator@cargotrace.test')->first();

        // 3. Update user
        $updateResponse = $this->put(route('users.update', $createdUser->id), [
            'name' => 'Updated Operator Account',
            'email' => 'newoperator@cargotrace.test',
            'role' => 'operator',
            'company_name' => 'CargoTrace Operations Updated',
            'phone' => '+628999999999',
            'preferred_currency' => 'EUR',
        ]);
        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('users', ['company_name' => 'CargoTrace Operations Updated']);

        // 4. Delete user
        $deleteResponse = $this->delete(route('users.destroy', $createdUser->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $createdUser->id]);
    }

    public function test_operator_and_customer_cannot_access_user_management(): void
    {
        // Operator cannot access
        $this->actingAs($this->operator);
        $res1 = $this->get(route('users.index'));
        $res1->assertStatus(403);

        // Customer cannot access
        $this->actingAs($this->customer1);
        $res2 = $this->get(route('users.index'));
        $res2->assertStatus(403);
    }

    public function test_non_admin_cannot_manage_carriers_or_locations(): void
    {
        // Customer cannot access carriers or locations index
        $this->actingAs($this->customer1);
        $this->get(route('carriers.index'))->assertStatus(403);
        $this->get(route('locations.index'))->assertStatus(403);

        // Operator cannot mutate carriers
        $this->actingAs($this->operator);
        $this->post(route('carriers.store'), [
            'name' => 'Unauthorized Carrier',
            'code' => 'UAC',
            'service_type' => 'SEA_FREIGHT',
        ])->assertStatus(403);
    }

    public function test_customer_can_only_view_own_shipment_details_and_not_mutate(): void
    {
        // Customer 1's shipment
        $shipment1 = Shipment::where('customer_id', $this->customer1->id)->first();
        // Customer 2's shipment
        $shipment2 = Shipment::where('customer_id', $this->customer2->id)->first();

        $this->actingAs($this->customer1);

        // Can view own shipment
        if ($shipment1) {
            $viewRes = $this->get(route('shipments.show', $shipment1->id));
            $viewRes->assertStatus(200);
            $viewRes->assertSee($shipment1->shipment_number);
            // Must not see edit or progression buttons
            $viewRes->assertDontSee('Transition Status');
            $viewRes->assertDontSee('+ Raise Exception');
        }

        // Forbidden from viewing Customer 2's shipment
        if ($shipment2) {
            $unauthorizedRes = $this->get(route('shipments.show', $shipment2->id));
            $unauthorizedRes->assertStatus(403);
        }

        // Customer cannot mutate status
        if ($shipment1) {
            $statusRes = $this->post(route('shipments.status', $shipment1->id), ['status' => 'IN_TRANSIT']);
            $statusRes->assertStatus(403);
        }
    }

    public function test_public_tracker_is_integrated_seamlessly_with_dashboard(): void
    {
        // Accessing public tracker displays dashboard with live tracker
        $response = $this->get(route('public.track'));
        $response->assertStatus(200);
        $response->assertSee('Live Global Consignment Tracker');
        $response->assertSee('dashboardTrackerMap');

        // Accessing with track query loads specific consignment
        $shipment = Shipment::first();
        $trackedResponse = $this->get(route('public.track', ['track' => $shipment->shipment_number]));
        $trackedResponse->assertStatus(200);
        $trackedResponse->assertSee($shipment->shipment_number);
    }
}
