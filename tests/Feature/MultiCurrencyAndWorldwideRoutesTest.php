<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Location;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiCurrencyAndWorldwideRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_currency_service_converts_between_world_currencies(): void
    {
        $currencyService = app(CurrencyService::class);

        // Convert 100 USD to EUR (0.9234 rate)
        $eurAmount = $currencyService->convert(100.0, 'USD', 'EUR');
        $this->assertEquals(92.34, $eurAmount);

        // Convert 100 USD to IDR (15,850 rate)
        $idrAmount = $currencyService->convert(100.0, 'USD', 'IDR');
        $this->assertEquals(1585000.0, $idrAmount);

        // Cross conversion: EUR to IDR
        $eurToIdr = $currencyService->convert(92.34, 'EUR', 'IDR');
        $this->assertGreaterThan(1500000.0, $eurToIdr);
    }

    public function test_can_switch_session_currency(): void
    {
        $response = $this->post(route('currency.switch'), [
            'currency_code' => 'EUR',
        ]);

        $response->assertSessionHas('selected_currency', 'EUR');
    }

    public function test_api_currency_convert_endpoint(): void
    {
        $response = $this->postJson('/api/currencies/convert', [
            'amount' => 1000,
            'from' => 'USD',
            'to' => 'JPY',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'original_amount',
                'from_currency',
                'to_currency',
                'converted_amount',
                'formatted',
            ]);
    }

    public function test_worldwide_routes_span_all_continents(): void
    {
        $continents = Location::distinct()->pluck('continent')->toArray();

        $this->assertContains('Asia', $continents);
        $this->assertContains('Europe', $continents);
        $this->assertContains('North America', $continents);
        $this->assertContains('South America', $continents);
        $this->assertContains('Africa', $continents);
        $this->assertContains('Oceania', $continents);
    }
}
