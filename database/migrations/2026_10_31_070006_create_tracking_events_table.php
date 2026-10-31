<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('status'); // CREATED, BOOKED, PICKED_UP, IN_TRANSIT, CUSTOMS_HOLD, DELIVERED, etc.
            $table->string('location_name');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('event_time');
            $table->text('description');
            $table->boolean('is_exception')->default(false);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('weather_summary')->nullable();
            $table->decimal('weather_temperature', 5, 2)->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'event_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
