<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_switch_language_to_english_and_render_english_views(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Post to switch language to English
        $response = $this->actingAs($admin)
            ->post(route('language.switch'), ['locale' => 'en']);

        $response->assertSessionHas('locale', 'en');

        // 2. Visit dashboard with English session
        $dashboardResponse = $this->actingAs($admin)
            ->withSession(['locale' => 'en'])
            ->get(route('dashboard'));

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Total Shipments');
        $dashboardResponse->assertSee('In Transit');

        // 3. Visit user management with English session
        $usersResponse = $this->actingAs($admin)
            ->withSession(['locale' => 'en'])
            ->get(route('users.index'));

        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('User Management');

        // 4. Visit public track with English session
        $trackResponse = $this->withSession(['locale' => 'en'])
            ->get(route('public.track'));

        $trackResponse->assertStatus(200);
        $trackResponse->assertSee('Track Global Consignments');

        // 5. Visit shipments index with English session
        $shipmentsResponse = $this->actingAs($admin)
            ->withSession(['locale' => 'en'])
            ->get(route('shipments.index'));

        $shipmentsResponse->assertStatus(200);
        $shipmentsResponse->assertSee('Shipments');

        // 6. Visit shipment show with English session
        $santosShipment = \App\Models\Shipment::where('shipment_number', 'CT-2026-SA1029')->first();
        if ($santosShipment) {
            $showResponse = $this->actingAs($admin)
                ->withSession(['locale' => 'en'])
                ->get(route('shipments.show', $santosShipment->id));

            $showResponse->assertStatus(200);
            $showResponse->assertSee('Cargo loaded onboard Hapag-Lloyd vessel at Santos terminal.');
        }
    }

    public function test_can_switch_language_to_indonesian_and_render_indonesian_views(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Post to switch language to Indonesian
        $response = $this->actingAs($admin)
            ->post(route('language.switch'), ['locale' => 'id']);

        $response->assertSessionHas('locale', 'id');

        // 2. Visit dashboard with Indonesian session
        $dashboardResponse = $this->actingAs($admin)
            ->withSession(['locale' => 'id'])
            ->get(route('dashboard'));

        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Total Kargo');

        // 3. Visit user management with Indonesian session
        $usersResponse = $this->actingAs($admin)
            ->withSession(['locale' => 'id'])
            ->get(route('users.index'));

        $usersResponse->assertStatus(200);
        $usersResponse->assertSee('Manajemen Pengguna');

        // 4. Visit public track with Indonesian session
        $trackResponse = $this->withSession(['locale' => 'id'])
            ->get(route('public.track'));

        $trackResponse->assertStatus(200);
        $trackResponse->assertSee('Lacak Pengiriman Global');

        // 5. Visit shipments index with Indonesian session
        $shipmentsResponse = $this->actingAs($admin)
            ->withSession(['locale' => 'id'])
            ->get(route('shipments.index'));

        $shipmentsResponse->assertStatus(200);
        $shipmentsResponse->assertSee('Daftar Pengiriman');

        // 6. Visit shipment show with Indonesian session
        $santosShipment = \App\Models\Shipment::where('shipment_number', 'CT-2026-SA1029')->first();
        if ($santosShipment) {
            $showResponse = $this->actingAs($admin)
                ->withSession(['locale' => 'id'])
                ->get(route('shipments.show', $santosShipment->id));

            $showResponse->assertStatus(200);
            $showResponse->assertSee('Kargo dimuat ke atas kapal Hapag-Lloyd di terminal Santos.');
            $showResponse->assertSee('Pulp Kayu Putih Dikelantang Bersertifikat FSC');
            $showResponse->assertSee('Kargo Diambil');
        }
    }

    public function test_get_language_switcher_route(): void
    {
        $response = $this->get(route('language.switch.get', ['locale' => 'en']));
        $response->assertSessionHas('locale', 'en');
        $response->assertRedirect();
    }
}
