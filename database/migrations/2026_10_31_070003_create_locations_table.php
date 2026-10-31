<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique(); // SGSIN, NLRTM, CNSHA, USLAX, etc.
            $table->string('city');
            $table->string('country');
            $table->string('continent'); // Asia, Europe, North America, South America, Africa, Oceania
            $table->string('type')->default('SEAPORT'); // SEAPORT, AIRPORT, LOGISTICS_HUB
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('timezone')->default('UTC');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
