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

            $table->decimal('amountTo_pay')->nullable();
            $table->decimal('amount_paid')->nullable();
            $table->integer('referencie');

            $table->enum('payment_status', ['pendent', 'paid', 'faild'])->nullable();
            $table->enum('payment_method', ['card', 'cash', 'undefined'])
            ->default('undefined');

            $table->timestamps();
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
