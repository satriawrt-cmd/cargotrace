<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\EtaService;
use App\Services\ExceptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EtaDelayAndExceptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_eta_service_correctly_evaluates_on_time_and_delay_severities(): void
    {
        $etaService = app(EtaService::class);
        $expected = Carbon::parse('2026-11-10 12:00:00');

        // Case 1: Estimated arrival <= expected -> ON_TIME
        $res1 = $etaService->evaluateDelay($expected, Carbon::parse('2026-11-10 11:30:00'));
        $this->assertEquals('ON_TIME', $res1['delay_status']);
        $this->assertEquals(0, $res1['delay_hours']);
        $this->assertEquals('NONE', $res1['delay_severity']);

        // Case 2: Delay 4 hours -> LOW (<= 6 hours)
        $res2 = $etaService->evaluateDelay($expected, Carbon::parse('2026-11-10 16:00:00'));
        $this->assertEquals('DELAYED', $res2['delay_status']);
        $this->assertEquals(4, $res2['delay_hours']);
        $this->assertEquals('LOW', $res2['delay_severity']);

        // Case 3: Delay 14 hours -> MEDIUM (> 6 and <= 24 hours)
        $res3 = $etaService->evaluateDelay($expected, Carbon::parse('2026-11-11 02:00:00'));
        $this->assertEquals('DELAYED', $res3['delay_status']);
        $this->assertEquals(14, $res3['delay_hours']);
        $this->assertEquals('MEDIUM', $res3['delay_severity']);

        // Case 4: Delay 30 hours -> HIGH (> 24 hours)
        $res4 = $etaService->evaluateDelay($expected, Carbon::parse('2026-11-11 18:00:00'));
        $this->assertEquals('DELAYED', $res4['delay_status']);
        $this->assertEquals(30, $res4['delay_hours']);
        $this->assertEquals('HIGH', $res4['delay_severity']);
    }

    public function test_can_raise_and_resolve_shipment_exception(): void
    {
        $exceptionService = app(ExceptionService::class);
        $operator = User::where('role', 'operator')->first();
        $shipment = Shipment::where('current_status', Shipment::STATUS_IN_TRANSIT)->first();

        // 1. Raise Exception
        $exception = $exceptionService->createException($shipment, [
            'exception_type' => 'WEATHER_DELAY',
            'severity' => 'MEDIUM',
            'root_cause' => 'Severe maritime storm near Luzon Strait.',
            'delay_impact_hours' => 12,
        ], $operator);

        $this->assertDatabaseHas('shipment_exceptions', [
            'id' => $exception->id,
            'exception_type' => 'WEATHER_DELAY',
            'status' => 'OPEN',
        ]);

        $this->assertTrue($exception->isOpen());

        // Tracking event should be automatically logged
        $this->assertDatabaseHas('tracking_events', [
            'shipment_id' => $shipment->id,
            'is_exception' => true,
        ]);

        // 2. Resolve Exception
        $resolved = $exceptionService->resolveException($exception, [
            'resolution_notes' => 'Storm cleared. Vessel resumed route via alternative corridor.',
        ], $operator);

        $this->assertEquals('RESOLVED', $resolved->status);
        $this->assertNotNull($resolved->resolved_at);
        $this->assertFalse($resolved->isOpen());
    }
}
