<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('carrier_id')->constrained('carriers')->restrictOnDelete();
            $table->foreignId('origin_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('destination_id')->constrained('locations')->restrictOnDelete();
            $table->string('cargo_type');
            $table->decimal('weight_kg', 10, 2)->default(0.00);
            $table->decimal('volume_cbm', 10, 2)->default(0.00);

            $table->dateTime('expected_departure');
            $table->dateTime('actual_departure')->nullable();
            $table->dateTime('expected_arrival');
            $table->dateTime('estimated_arrival');
            $table->dateTime('actual_arrival')->nullable();

            $table->string('current_status')->default('CREATED'); // CREATED, BOOKED, PICKED_UP, IN_TRANSIT, DELIVERED, CANCELLED
            $table->string('delay_status')->default('ON_TIME'); // ON_TIME, DELAYED
            $table->integer('delay_hours')->default(0);
            $table->string('delay_severity')->default('NONE'); // NONE, LOW, MEDIUM, HIGH
            $table->text('delay_reason')->nullable();

            $table->string('current_location_name')->nullable();
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();

            // Multi-Currency and Transaction Data
            $table->string('currency_code', 3)->default('USD');
            $table->decimal('shipping_cost', 14, 2)->default(0.00);
            $table->decimal('declared_value', 14, 2)->default(0.00);
            $table->decimal('insurance_fee', 14, 2)->default(0.00);
            $table->decimal('total_cost', 14, 2)->default(0.00);

            $table->text('special_instructions')->nullable();
            $table->timestamps();

            $table->index(['current_status', 'delay_status']);
            $table->index('shipment_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
