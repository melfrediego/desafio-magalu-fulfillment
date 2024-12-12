<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\PendingTransaction;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransactionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TransactionService();
    }

    /**
     * Testa a operação de depósito.
     */
    public function test_deposit_updates_balance_correctly()
    {
        $account = Account::factory()->create([
            'balance' => 1000,
            'user_id' => \App\Models\User::factory()->create([
                'cpf_cnpj' => '12345678900', // Adiciona CPF/CNPJ válido
            ])->id,
        ]);

        $this->service->createTransactionRecord([
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 500,
            'description' => 'Depósito teste',
            'status' => 'success',
        ]);

        $this->assertEquals(1500, $account->fresh()->balance);
    }

    /**
     * Testa a operação de saque com taxa.
     */
    public function test_withdraw_applies_fee_and_updates_balance()
    {
        $account = Account::factory()->create([
            'balance' => 1000,
            'user_id' => \App\Models\User::factory()->create([
                'cpf_cnpj' => '12345678900', // Adiciona CPF/CNPJ válido
            ])->id,
        ]);

        $transaction = $this->service->createTransactionRecord([
            'account_id' => $account->id,
            'type' => 'withdraw',
            'amount' => 400,
            'description' => 'Saque teste',
            'status' => 'success',
        ]);

        $this->assertEquals(600, $account->fresh()->balance);
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    /**
     * Testa a operação de transferência entre contas.
     */
    public function test_transfer_updates_both_accounts_correctly()
    {
        $sourceAccount = Account::factory()->create([
            'balance' => 1000,
            'user_id' => \App\Models\User::factory()->create([
                'cpf_cnpj' => '12345678900', // Adiciona CPF/CNPJ válido
            ])->id,
        ]);

        $targetAccount = Account::factory()->create([
            'balance' => 500,
            'user_id' => \App\Models\User::factory()->create([
                'cpf_cnpj' => '98765432100', // Adiciona CPF/CNPJ válido
            ])->id,
        ]);

        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $sourceAccount->id,
            'target_account_id' => $targetAccount->id,
            'amount' => 300,
            'type' => 'transfer',
            'description' => 'Transferência teste',
        ]);

        $this->service->processPendingTransaction($pendingTransaction);

        $this->assertEquals(700, $sourceAccount->fresh()->balance);
        $this->assertEquals(800, $targetAccount->fresh()->balance);
    }

    /**
     * Testa saque com saldo insuficiente.
     */
    public function test_withdraw_fails_on_insufficient_balance()
    {
        $account = Account::factory()->create([
            'balance' => 100,
            'user_id' => \App\Models\User::factory()->create([
                'cpf_cnpj' => '12345678900', // Adiciona CPF/CNPJ válido
            ])->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Saldo insuficiente.');

        $this->service->createTransactionRecord([
            'account_id' => $account->id,
            'type' => 'withdraw',
            'amount' => 200,
            'description' => 'Saque inválido',
        ]);
    }
}
