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
            $table->string('cpf_cnpj')->unique()->nullable()->after('phone_whatsapp'); // CPF ou CNPJ
            $table->enum('person_type', ['PF', 'PJ'])->nullable()->after('cpf_cnpj'); // Tipo de pessoa: Física (PF) ou Jurídica (PJ)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cpf_cnpj', 'person_type']);
        });
    }
};
