<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignId('tracking_event_id')->nullable()->constrained('tracking_events')->nullOnDelete();
            $table->string('exception_type'); // CUSTOMS_HOLD, WEATHER_DELAY, PORT_CONGESTION, DAMAGED, MISROUTED, DOCUMENTATION_ISSUE
            $table->string('severity')->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
            $table->string('status')->default('OPEN'); // OPEN, INVESTIGATING, RESOLVED
            $table->dateTime('detected_at');
            $table->dateTime('resolved_at')->nullable();
            $table->text('root_cause');
            $table->text('action_plan')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['shipment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_exceptions');
    }
};
