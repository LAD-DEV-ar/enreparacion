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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocios_id')->constrained('negocios')->cascadeOnDelete();
            $table->string('canal')->default('email'); // email, whatsapp, etc.
            $table->string('evento'); // recibido, en_reparacion, listo, entregado
            $table->string('titulo')->nullable(); // Asunto del correo o título
            $table->text('mensaje'); // Plantilla de texto con placeholders
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['negocios_id', 'canal', 'evento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
