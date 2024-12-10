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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_whatsapp')->nullable()->after('password'); // Telefone com WhatsApp
            $table->date('birth_date')->nullable()->after('phone_whatsapp'); // Data de nascimento
            $table->boolean('is_client')->default(false)->after('birth_date'); // Flag para identificar cliente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_whatsapp', 'birth_date', 'is_client']);
        });
    }
};
