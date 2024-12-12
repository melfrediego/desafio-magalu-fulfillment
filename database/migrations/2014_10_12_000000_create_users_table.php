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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_whatsapp')->nullable(); // Telefone com WhatsApp
            $table->date('birth_date')->nullable(); // Data de nascimento
            $table->boolean('is_client')->default(false); // Flag para identificar cliente
            $table->string('cpf_cnpj')->nullable(); // CPF ou CNPJ
            $table->enum('person_type', ['PF', 'PJ'])->nullable(); // Tipo de pessoa: Física (PF) ou Jurídica (PJ)
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
