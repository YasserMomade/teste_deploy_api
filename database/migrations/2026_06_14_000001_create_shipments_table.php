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
            $table->string('reference')->unique();
            $table->string('carta_porte');               
            $table->enum('airline', ['TAAG', 'TAP']);  
            $table->string('origin')->default('Lisboa');
            $table->string('destination')->default('Maputo');
            $table->date('shipment_date');              
            $table->text('general_observations')->nullable(); 
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipment_id')->nullable()->after('store_id')
                  ->constrained('shipments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipment_id']);
            $table->dropColumn('shipment_id');
        });

        Schema::dropIfExists('shipments');
    }
};
