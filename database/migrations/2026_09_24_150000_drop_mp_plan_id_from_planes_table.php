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
        if (! Schema::hasColumn('planes', 'mp_plan_id')) {
            return;
        }

        if (Schema::hasIndex('planes', 'planes_mp_plan_id_unique')) {
            Schema::table('planes', function (Blueprint $table) {
                $table->dropUnique('planes_mp_plan_id_unique');
            });
        }

        Schema::table('planes', function (Blueprint $table) {
            $table->dropColumn('mp_plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('planes', 'mp_plan_id')) {
            return;
        }

        Schema::table('planes', function (Blueprint $table) {
            $table->string('mp_plan_id')->nullable()->unique()->after('activo');
        });
    }
};
