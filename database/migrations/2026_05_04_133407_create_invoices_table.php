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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->decimal('amountTo_pay');
            $table->decimal('amount_paid');
            $table->enum('payment_status', ['pendent', 'paid', 'faild']);
            $table->enum('payment_method', ['card', 'cash']);
            $table->timestamps();
            
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
