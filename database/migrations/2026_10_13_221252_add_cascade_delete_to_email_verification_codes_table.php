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
        Schema::table('email_verification_codes', function (Blueprint $table) {
            $table->dropForeign(['users_id']);

            $table->foreign('users_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_verification_codes', function (Blueprint $table) {
            $table->dropForeign(['users_id']);

            $table->foreign('users_id')
                ->references('id')
                ->on('users');
        });
    }
};
