<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transit_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('countries')->cascadeOnDelete();
            $table->foreignId('destination_country_id')->constrained('countries')->cascadeOnDelete();
            $table->enum('service_type', ['expresso', 'normal']);
            $table->unsignedInteger('expected_hours')->comment('Expected transit duration in hours');
            $table->json('departure_days')->comment('Days of week shipments depart: 1=Mon, 2=Tue ... 7=Sun');
            $table->timestamps();

            $table->unique([
                'origin_country_id',
                'destination_country_id',
                'service_type',
            ], 'unique_transit_route');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transit_times');
    }
};
