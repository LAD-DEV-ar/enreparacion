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
        Schema::create('notificacion_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('negocios_id')->constrained('negocios')->cascadeOnDelete();
            $table->foreignId('reparaciones_id')->nullable()->constrained('reparaciones')->nullOnDelete();
            $table->foreignId('clientes_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('users_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('canal')->default('email'); // email, whatsapp, etc.
            $table->string('destinatario'); // Correo electrónico o teléfono
            $table->string('asunto')->nullable();
            $table->text('mensaje'); // Contenido enviado final renderizado
            $table->string('estado_envio')->default('enviado'); // enviado, fallido, pendiente
            $table->text('error_mensaje')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacion_clientes');
    }
};
