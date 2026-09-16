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
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocios_id')->constrained('negocios');
            $table->foreignId('plan_id')->constrained('planes');
            $table->boolean('estado');
            $table->timestamp('inicio');
            $table->timestamp('fin');
            $table->timestamp('ultimo_pago');
            $table->timestamp('proxima_facturacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
