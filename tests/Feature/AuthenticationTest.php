<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@cargotrace.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@cargotrace.test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_admin_audit_logs(): void
    {
        $customer = User::where('role', 'customer')->first();

        $response = $this->actingAs($customer)->get(route('audit-logs.index'));
        $response->assertStatus(403);
    }

    public function test_api_login_returns_sanctum_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'operator@cargotrace.test',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user',
                'token',
            ]);
    }
}
