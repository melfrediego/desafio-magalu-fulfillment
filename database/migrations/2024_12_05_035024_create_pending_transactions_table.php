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
        Schema::create('pending_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade'); // Conta de origem
            $table->foreignId('target_account_id')->nullable()->constrained('accounts')->onDelete('cascade'); // Conta de destino (nullable)
            $table->string('transaction_id')->nullable(); // Permite valores nulos
            $table->enum('type', ['deposit', 'withdraw', 'transfer']);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_transactions');
    }
};
