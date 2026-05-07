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
        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->enum('descryption', ['recebido_lisboa', 'em_processamento', 'pronto_expedicao', 'expedido', 'em_transito', 'recebido_mocambique', 'pronto_levantamento', 'entregue'])->default('recebido_lisboa');
            $table->softDeletes();

            $table->foreignId('responsible_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

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
