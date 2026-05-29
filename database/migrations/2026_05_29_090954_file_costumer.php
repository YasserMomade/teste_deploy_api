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
        Schema::create('files_costumer', function (Blueprint $table) {

            $table->id();
            $table->string('document_type');
            $table->string('url');
            $table->softDeletes();

            $table->foreignId('costumer_id')->constrained('costumers')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
           
    }
};
