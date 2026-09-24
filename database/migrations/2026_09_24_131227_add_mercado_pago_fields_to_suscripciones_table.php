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
        Schema::table('suscripciones', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->change();
            $table->string('tipo')->default('trial')->after('plan_id');
            $table->string('mp_preapproval_id')->nullable()->after('tipo');
            $table->string('mp_status')->nullable()->after('mp_preapproval_id');
            $table->json('metadatos')->nullable()->after('mp_status');
            $table->timestamp('ultimo_pago')->nullable()->change();
            $table->timestamp('proxima_facturacion')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suscripciones', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'mp_preapproval_id', 'mp_status', 'metadatos']);
        });
    }
};
