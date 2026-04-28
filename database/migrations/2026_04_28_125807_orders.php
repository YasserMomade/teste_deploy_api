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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('tracking')->unique();
            $table->string('origin');
            $table->string('destination');
            $table->date('reception_date');
            $table->string('service_type');
            $table->text('description')->nullable();
            $table->integer('volume_number');
            $table->decimal('weight', 10, 2);
            $table->decimal('declared_weight', 10, 2)->nullable();
            $table->decimal('value', 10, 2);
            $table->string('payment_status');
            $table->string('payment_method');
            $table->softDeletes();


            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('id_responsavel')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
