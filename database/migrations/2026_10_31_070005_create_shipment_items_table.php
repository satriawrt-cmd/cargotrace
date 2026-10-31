<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('item_description');
            $table->integer('quantity')->default(1);
            $table->string('package_type')->default('Box'); // Box, Pallet, Container, Crate, Drum
            $table->decimal('weight_kg', 10, 2)->default(0.00);
            $table->decimal('declared_value', 14, 2)->default(0.00);
            $table->string('hs_code', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};
