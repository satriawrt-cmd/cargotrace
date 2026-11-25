<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $operator = User::where('role', 'operator')->first();
        $customer1 = User::where('email', 'customer@globaltrade.test')->first();
        $customer2 = User::where('email', 'customer2@pacificbrands.test')->first();

        $maersk = Carrier::where('code', 'MAEU')->first();
        $msc = Carrier::where('code', 'MSCU')->first();
        $cma = Carrier::where('code', 'CMDU')->first();
        $hapag = Carrier::where('code', 'HLCU')->first();
        $dhl = Carrier::where('code', 'DHLG')->first();

        $shanghai = Location::where('code', 'CNSHA')->first();
        $rotterdam = Location::where('code', 'NLRTM')->first();
        $singapore = Location::where('code', 'SGSIN')->first();
        $sydney = Location::where('code', 'AUSYD')->first();
        $jakarta = Location::where('code', 'IDTPP')->first();
        $losAngeles = Location::where('code', 'USLAX')->first();
        $santos = Location::where('code', 'BRSSZ')->first();
        $hamburg = Location::where('code', 'DEHAM')->first();
        $dubai = Location::where('code', 'AEDXB')->first();
        $capeTown = Location::where('code', 'ZACPT')->first();
        $suez = Location::where('code', 'EGPSD')->first();

        // -------------------------------------------------------------
        // Shipment 1: Asia -> Europe (Shanghai to Rotterdam via Suez)
        // Multi-Currency: EUR | Status: IN_TRANSIT
        // -------------------------------------------------------------
        $s1Departure = Carbon::now()->subDays(12);
        $s1ExpectedArrival = Carbon::now()->addDays(14);
        $s1EstimatedArrival = $s1ExpectedArrival->copy(); // On time

        $shipment1 = Shipment::create([
            'shipment_number' => 'CT-2026-EU4091',
            'customer_id' => $customer1->id,
            'carrier_id' => $maersk->id,
            'origin_id' => $shanghai->id,
            'destination_id' => $rotterdam->id,
            'cargo_type' => 'Consumer Electronics & Solar Panels',
            'weight_kg' => 24500.00,
            'volume_cbm' => 68.50,
            'expected_departure' => $s1Departure,
            'actual_departure' => $s1Departure,
            'expected_arrival' => $s1ExpectedArrival,
            'estimated_arrival' => $s1EstimatedArrival,
            'current_status' => Shipment::STATUS_IN_TRANSIT,
            'delay_status' => 'ON_TIME',
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_location_name' => 'Transit Hub: ' . $suez->name . ' (' . $suez->country . ')',
            'current_latitude' => $suez->latitude,
            'current_longitude' => $suez->longitude,
            'currency_code' => 'EUR',
            'shipping_cost' => 4850.00,
            'declared_value' => 125000.00,
            'insurance_fee' => 375.00,
            'total_cost' => 5225.00,
            'special_instructions' => 'Maintain temperature-controlled container at 20°C. High value lithium equipment.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'item_description' => 'Industrial Solar Inverters & Cells',
            'quantity' => 120,
            'package_type' => 'Pallet',
            'weight_kg' => 14000.00,
            'declared_value' => 75000.00,
            'hs_code' => '8504.40.95',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment1->id,
            'item_description' => 'Smart Home Control Panels',
            'quantity' => 450,
            'package_type' => 'Box',
            'weight_kg' => 10500.00,
            'declared_value' => 50000.00,
            'hs_code' => '8537.10.91',
        ]);

        // Historical checkpoints for Shipment 1
        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'status' => Shipment::STATUS_CREATED,
            'location_name' => $shanghai->name . ', ' . $shanghai->country,
            'latitude' => $shanghai->latitude,
            'longitude' => $shanghai->longitude,
            'event_time' => $s1Departure->copy()->subDays(2),
            'description' => 'Booking confirmed and export documentation lodged at Shanghai Port.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Overcast (18.5°C)',
            'weather_temperature' => 18.5,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'status' => Shipment::STATUS_PICKED_UP,
            'location_name' => 'Shanghai Waigaoqiao Terminal',
            'latitude' => $shanghai->latitude,
            'longitude' => $shanghai->longitude,
            'event_time' => $s1Departure->copy()->subHours(12),
            'description' => 'Container sealed and loaded onto vessel Maersk Mc-Kinney Moller.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Clear Sky (20.0°C)',
            'weather_temperature' => 20.0,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'South China Sea / Singapore Strait',
            'latitude' => $singapore->latitude,
            'longitude' => $singapore->longitude,
            'event_time' => $s1Departure->copy()->addDays(5),
            'description' => 'Bunkering stop and maritime corridor clearance at Singapore anchorage.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Tropical Fair (29.2°C)',
            'weather_temperature' => 29.2,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment1->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => $suez->name . ' (' . $suez->country . ')',
            'latitude' => $suez->latitude,
            'longitude' => $suez->longitude,
            'event_time' => Carbon::now()->subHours(8),
            'description' => 'Vessel successfully queued and entered Northbound Suez Canal transit convoy.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Clear Sky (26.4°C)',
            'weather_temperature' => 26.4,
        ]);

        // -------------------------------------------------------------
        // Shipment 2: Southeast Asia -> Australia (Singapore to Sydney)
        // Multi-Currency: SGD | Status: IN_TRANSIT | DELAYED with Exception
        // Demonstrates CUSTOMS_HOLD & Weather delay threshold
        // -------------------------------------------------------------
        $s2Departure = Carbon::now()->subDays(6);
        $s2ExpectedArrival = Carbon::now()->addDays(2);
        $s2EstimatedArrival = $s2ExpectedArrival->copy()->addHours(18); // 18 hours delay (MEDIUM)

        $shipment2 = Shipment::create([
            'shipment_number' => 'CT-2026-OC8821',
            'customer_id' => $customer1->id,
            'carrier_id' => $cma->id,
            'origin_id' => $singapore->id,
            'destination_id' => $sydney->id,
            'cargo_type' => 'Pharmaceutical Vaccines & Cold Chain Supplies',
            'weight_kg' => 8400.00,
            'volume_cbm' => 24.00,
            'expected_departure' => $s2Departure,
            'actual_departure' => $s2Departure,
            'expected_arrival' => $s2ExpectedArrival,
            'estimated_arrival' => $s2EstimatedArrival,
            'current_status' => Shipment::STATUS_IN_TRANSIT,
            'delay_status' => 'DELAYED',
            'delay_hours' => 18,
            'delay_severity' => 'MEDIUM',
            'delay_reason' => 'Delay of 18h due to severe tropical swell and mandatory customs biosecurity inspection.',
            'current_location_name' => 'Coral Sea Approach / Port of Sydney outer anchorage',
            'current_latitude' => -33.8500,
            'current_longitude' => 151.2500,
            'currency_code' => 'SGD',
            'shipping_cost' => 6400.00,
            'declared_value' => 210000.00,
            'insurance_fee' => 1250.00,
            'total_cost' => 7650.00,
            'special_instructions' => 'Continuous temperature logger verification required upon Australian biosecurity clearance.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment2->id,
            'item_description' => 'Temperature-monitored Vaccine Vials',
            'quantity' => 800,
            'package_type' => 'Container',
            'weight_kg' => 8400.00,
            'declared_value' => 210000.00,
            'hs_code' => '3002.41.00',
        ]);

        // Tracking events for Shipment 2
        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'status' => Shipment::STATUS_BOOKED,
            'location_name' => $singapore->name . ', ' . $singapore->country,
            'latitude' => $singapore->latitude,
            'longitude' => $singapore->longitude,
            'event_time' => $s2Departure->copy()->subDay(),
            'description' => 'Cold chain container plugged into power grid at PSA Singapore Terminal.',
            'created_by_user_id' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Java Sea Maritime Corridor',
            'latitude' => -5.2000,
            'longitude' => 112.5000,
            'event_time' => $s2Departure->copy()->addDays(2),
            'description' => 'Vessel on course towards Torres Strait.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Moderate Rain Showers (28.0°C)',
            'weather_temperature' => 28.0,
        ]);

        $excEvent = TrackingEvent::create([
            'shipment_id' => $shipment2->id,
            'status' => 'EXCEPTION',
            'location_name' => 'Coral Sea Outer Anchorage',
            'latitude' => -33.8500,
            'longitude' => 151.2500,
            'event_time' => Carbon::now()->subHours(14),
            'description' => '[EXCEPTION: CUSTOMS_HOLD] Australian Department of Agriculture biosecurity hold on cold-chain manifests.',
            'is_exception' => true,
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Thunderstorm (21.0°C)',
            'weather_temperature' => 21.0,
        ]);

        // Active open exception on shipment 2
        ShipmentException::create([
            'shipment_id' => $shipment2->id,
            'tracking_event_id' => $excEvent->id,
            'exception_type' => 'CUSTOMS_HOLD',
            'severity' => 'MEDIUM',
            'status' => 'INVESTIGATING',
            'detected_at' => Carbon::now()->subHours(14),
            'root_cause' => 'Discrepancy in biosecurity import declaration certificate from export authority.',
            'action_plan' => 'Submitting revised batch serial certificates to Department of Agriculture portal.',
            'reported_by_user_id' => $operator->id,
        ]);

        Notification::create([
            'user_id' => $customer1->id,
            'shipment_id' => $shipment2->id,
            'type' => 'EXCEPTION_ALERT',
            'title' => "Customs Hold on {$shipment2->shipment_number}",
            'message' => 'Shipment subject to biosecurity verification at Sydney Port. Estimated delay: 18 hours.',
            'action_url' => '/shipments/' . $shipment2->id,
        ]);

        // -------------------------------------------------------------
        // Shipment 3: Asia -> North America (Jakarta to Los Angeles)
        // Multi-Currency: USD | Status: BOOKED
        // -------------------------------------------------------------
        $s3Departure = Carbon::now()->addDays(2);
        $s3ExpectedArrival = Carbon::now()->addDays(22);

        $shipment3 = Shipment::create([
            'shipment_number' => 'CT-2026-NA3341',
            'customer_id' => $customer2->id,
            'carrier_id' => $msc->id,
            'origin_id' => $jakarta->id,
            'destination_id' => $losAngeles->id,
            'cargo_type' => 'Sustainable Indonesian Coffee & Cacao',
            'weight_kg' => 18000.00,
            'volume_cbm' => 52.00,
            'expected_departure' => $s3Departure,
            'expected_arrival' => $s3ExpectedArrival,
            'estimated_arrival' => $s3ExpectedArrival,
            'current_status' => Shipment::STATUS_BOOKED,
            'delay_status' => 'ON_TIME',
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_location_name' => $jakarta->name . ' (' . $jakarta->city . ', Indonesia)',
            'current_latitude' => $jakarta->latitude,
            'current_longitude' => $jakarta->longitude,
            'currency_code' => 'USD',
            'shipping_cost' => 3800.00,
            'declared_value' => 64000.00,
            'insurance_fee' => 190.00,
            'total_cost' => 3990.00,
            'special_instructions' => 'Desiccant bags inserted inside container to prevent moisture sweating during Pacific crossing.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment3->id,
            'item_description' => 'Arabica Gayo Specialty Green Beans',
            'quantity' => 300,
            'package_type' => 'Crate',
            'weight_kg' => 18000.00,
            'declared_value' => 64000.00,
            'hs_code' => '0901.11.00',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment3->id,
            'status' => Shipment::STATUS_BOOKED,
            'location_name' => $jakarta->name . ', Indonesia',
            'latitude' => $jakarta->latitude,
            'longitude' => $jakarta->longitude,
            'event_time' => Carbon::now()->subHours(6),
            'description' => 'Containers gated in and scheduled for loading on MSC Isabella voyage 2603.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Mainly Clear (30.5°C)',
            'weather_temperature' => 30.5,
        ]);

        // -------------------------------------------------------------
        // Shipment 4: South America -> Europe (Santos, Brazil to Hamburg, Germany)
        // Multi-Currency: USD | Status: DELIVERED (Terminal State Demonstration)
        // -------------------------------------------------------------
        $s4Departure = Carbon::now()->subDays(28);
        $s4ExpectedArrival = Carbon::now()->subDays(2);
        $s4ActualArrival = Carbon::now()->subDays(2)->addHours(4);

        $shipment4 = Shipment::create([
            'shipment_number' => 'CT-2026-SA1029',
            'customer_id' => $customer2->id,
            'carrier_id' => $hapag->id,
            'origin_id' => $santos->id,
            'destination_id' => $hamburg->id,
            'cargo_type' => 'Specialty Wood & Cellulose Pulp',
            'weight_kg' => 42000.00,
            'volume_cbm' => 88.00,
            'expected_departure' => $s4Departure,
            'actual_departure' => $s4Departure,
            'expected_arrival' => $s4ExpectedArrival,
            'estimated_arrival' => $s4ExpectedArrival,
            'actual_arrival' => $s4ActualArrival,
            'current_status' => Shipment::STATUS_DELIVERED,
            'delay_status' => 'ON_TIME',
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_location_name' => $hamburg->name . ' (Warehouse Terminal C, Hamburg)',
            'current_latitude' => $hamburg->latitude,
            'current_longitude' => $hamburg->longitude,
            'currency_code' => 'USD',
            'shipping_cost' => 5600.00,
            'declared_value' => 88000.00,
            'insurance_fee' => 264.00,
            'total_cost' => 5864.00,
            'special_instructions' => 'FSC Certified documentation handed over upon discharge.',
        ]);

        ShipmentItem::create([
            'shipment_id' => $shipment4->id,
            'item_description' => 'FSC Certified Bleached Eucalyptus Pulp',
            'quantity' => 1200,
            'package_type' => 'Pallet',
            'weight_kg' => 42000.00,
            'declared_value' => 88000.00,
            'hs_code' => '4703.21.00',
        ]);

        // Historical checkpoints for Delivered shipment
        TrackingEvent::create([
            'shipment_id' => $shipment4->id,
            'status' => Shipment::STATUS_PICKED_UP,
            'location_name' => $santos->name . ', Brazil',
            'latitude' => $santos->latitude,
            'longitude' => $santos->longitude,
            'event_time' => $s4Departure,
            'description' => 'Cargo loaded onboard Hapag-Lloyd vessel at Santos terminal.',
            'created_by_user_id' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment4->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Atlantic Ocean Crossing',
            'latitude' => 15.0000,
            'longitude' => -25.0000,
            'event_time' => $s4Departure->copy()->addDays(14),
            'description' => 'Mid-Atlantic navigation corridor completed without anomalies.',
            'created_by_user_id' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment4->id,
            'status' => Shipment::STATUS_DELIVERED,
            'location_name' => $hamburg->name . ', Germany',
            'latitude' => $hamburg->latitude,
            'longitude' => $hamburg->longitude,
            'event_time' => $s4ActualArrival,
            'description' => 'Consignment successfully discharged, customs cleared, and signed off by consignee.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Partly Cloudy (14.2°C)',
            'weather_temperature' => 14.2,
        ]);

        // -------------------------------------------------------------
        // Shipment 5: Middle East -> Africa (Dubai to Cape Town)
        // Multi-Currency: AED | Status: PICKED_UP
        // -------------------------------------------------------------
        $s5Departure = Carbon::now()->subDays(1);
        $s5ExpectedArrival = Carbon::now()->addDays(15);

        $shipment5 = Shipment::create([
            'shipment_number' => 'CT-2026-AF7150',
            'customer_id' => $customer1->id,
            'carrier_id' => $dhl->id,
            'origin_id' => $dubai->id,
            'destination_id' => $capeTown->id,
            'cargo_type' => 'Telecommunications Fiber Optics & Network Hardware',
            'weight_kg' => 12500.00,
            'volume_cbm' => 36.00,
            'expected_departure' => $s5Departure,
            'actual_departure' => $s5Departure,
            'expected_arrival' => $s5ExpectedArrival,
            'estimated_arrival' => $s5ExpectedArrival,
            'current_status' => Shipment::STATUS_PICKED_UP,
            'delay_status' => 'ON_TIME',
            'delay_hours' => 0,
            'delay_severity' => 'NONE',
            'current_location_name' => $dubai->name . ', UAE',
            'current_latitude' => $dubai->latitude,
            'current_longitude' => $dubai->longitude,
            'currency_code' => 'AED',
            'shipping_cost' => 18500.00,
            'declared_value' => 340000.00,
            'insurance_fee' => 1020.00,
            'total_cost' => 19520.00,
            'special_instructions' => 'Handle with shock sensors. Fragile precision optical glass.',
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment5->id,
            'status' => Shipment::STATUS_PICKED_UP,
            'location_name' => $dubai->name . ', UAE',
            'latitude' => $dubai->latitude,
            'longitude' => $dubai->longitude,
            'event_time' => $s5Departure,
            'description' => 'Export inspection cleared and container stowed on container deck at Jebel Ali.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Clear Sky (32.0°C)',
            'weather_temperature' => 32.0,
        ]);

        // -------------------------------------------------------------
        // Shipment 6: Asia -> Asia (Tokyo to Jakarta)
        // Multi-Currency: IDR | Status: IN_TRANSIT | Resolved Exception
        // Demonstrates IDR currency & Resolved Exception lifecycle
        // -------------------------------------------------------------
        $s6Departure = Carbon::now()->subDays(4);
        $s6ExpectedArrival = Carbon::now()->addDays(5);
        $s6EstimatedArrival = $s6ExpectedArrival->copy()->addHours(6); // 6h delay (LOW)

        $shipment6 = Shipment::create([
            'shipment_number' => 'CT-2026-AS5512',
            'customer_id' => $customer1->id,
            'carrier_id' => $maersk->id,
            'origin_id' => Location::where('code', 'JPTYO')->first()->id,
            'destination_id' => $jakarta->id,
            'cargo_type' => 'Automotive Precision Transmissions & Sensors',
            'weight_kg' => 15200.00,
            'volume_cbm' => 41.00,
            'expected_departure' => $s6Departure,
            'actual_departure' => $s6Departure,
            'expected_arrival' => $s6ExpectedArrival,
            'estimated_arrival' => $s6EstimatedArrival,
            'current_status' => Shipment::STATUS_IN_TRANSIT,
            'delay_status' => 'DELAYED',
            'delay_hours' => 6,
            'delay_severity' => 'LOW',
            'delay_reason' => 'Port congestion during transit call delayed departure by 6 hours.',
            'current_location_name' => 'South China Sea / Luzon Strait',
            'current_latitude' => 20.5000,
            'current_longitude' => 120.5000,
            'currency_code' => 'IDR',
            'shipping_cost' => 54000000.00,
            'declared_value' => 850000000.00,
            'insurance_fee' => 2550000.00,
            'total_cost' => 56550000.00,
            'special_instructions' => 'Direct transfer to Cikarang Dry Port bonded zone.',
        ]);

        $resolvedExcEvent = TrackingEvent::create([
            'shipment_id' => $shipment6->id,
            'status' => 'EXCEPTION',
            'location_name' => 'Kaohsiung Feeder Anchorage',
            'latitude' => 22.6100,
            'longitude' => 120.2800,
            'event_time' => Carbon::now()->subDays(2),
            'description' => '[EXCEPTION: PORT_CONGESTION] Berth queue congested due to prior typhoon backlog.',
            'is_exception' => true,
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Moderate Rain (25.0°C)',
            'weather_temperature' => 25.0,
        ]);

        ShipmentException::create([
            'shipment_id' => $shipment6->id,
            'tracking_event_id' => $resolvedExcEvent->id,
            'exception_type' => 'PORT_CONGESTION',
            'severity' => 'LOW',
            'status' => 'RESOLVED',
            'detected_at' => Carbon::now()->subDays(2),
            'resolved_at' => Carbon::now()->subDay(),
            'root_cause' => 'Berth backlog from previous regional storm.',
            'action_plan' => 'Priority slot negotiated with terminal management.',
            'resolution_notes' => 'Berthing slot secured. Cargo transferred to outbound feeder vessel.',
            'reported_by_user_id' => $operator->id,
            'resolved_by_user_id' => $operator->id,
        ]);

        TrackingEvent::create([
            'shipment_id' => $shipment6->id,
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location_name' => 'Luzon Strait / En Route to Jakarta',
            'latitude' => 20.5000,
            'longitude' => 120.5000,
            'event_time' => Carbon::now()->subHours(12),
            'description' => 'Transit resumed on schedule after terminal clearance.',
            'created_by_user_id' => $operator->id,
            'weather_summary' => 'Clear Sky (28.5°C)',
            'weather_temperature' => 28.5,
        ]);

        // Audit Log Sample
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'SEED_INITIAL_DATA',
            'entity_type' => 'System',
            'entity_id' => 1,
            'new_values' => ['message' => 'CargoTrace international shipments initialized successfully.'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Console Seeder',
        ]);
    }
}
