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
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocios_id')->constrained('negocios');
            $table->foreignId('dispositivos_id')->constrained('dispositivos');
            $table->foreignId('users_id')->constrained('users');
            $table->string('falla_reportada');
            $table->string('clave_de_acceso')->nullable();
            $table->string('estado')->default('recibido');
            $table->unsignedBigInteger('costo_estimado')->nullable();
            $table->unsignedBigInteger('sena')->nullable();
            $table->string('notas_internas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reparaciones');
    }
};
