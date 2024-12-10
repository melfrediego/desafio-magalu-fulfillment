<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionService $service;

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
        // Configura conta inicial com saldo de 1000
        $account = Account::factory()->create(['balance' => 1000]);

        // Executa o depósito
        $transaction = $this->service->processTransaction($account, 'deposit', 500, 'Teste de depósito');

        // Verifica se o saldo foi atualizado
        $this->assertEquals(1500, $account->fresh()->balance);

        // Verifica se a transação foi registrada com sucesso
        $this->assertEquals('success', $transaction->status);
    }

    /**
     * Testa a operação de saque.
     */
    public function test_withdraw_applies_fee_and_updates_balance()
    {
        // Configura conta inicial com saldo e limite de crédito
        $account = Account::factory()->create(['balance' => 1000, 'credit_limit' => 500]);

        // Executa o saque
        $transaction = $this->service->processTransaction($account, 'withdraw', 400, 'Teste de saque');

        // Verifica se o saldo foi atualizado corretamente (com taxa de 1%)
        $this->assertEquals(596, $account->fresh()->balance); // 400 + 1% = 404

        // Verifica se a transação foi registrada com sucesso
        $this->assertEquals('success', $transaction->status);
    }

    /**
     * Testa a operação de transferência entre contas.
     */
    public function test_transfer_updates_both_accounts_correctly()
    {
        // Configura contas de origem e destino
        $sourceAccount = Account::factory()->create(['balance' => 1000, 'credit_limit' => 500]);
        $targetAccount = Account::factory()->create(['balance' => 500]);

        // Executa a transferência
        $this->service->transfer($sourceAccount, $targetAccount, 300, 'Teste de transferência');

        // Verifica os saldos atualizados
        $this->assertEquals(694, $sourceAccount->fresh()->balance); // 300 + 2% = 306
        $this->assertEquals(800, $targetAccount->fresh()->balance);
    }

    /**
     * Testa se uma transação com saldo insuficiente falha.
     */
    public function test_withdraw_fails_on_insufficient_balance()
    {
        // Configura conta inicial com saldo insuficiente
        $account = Account::factory()->create(['balance' => 100]);

        // Tenta executar o saque
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Saldo insuficiente, incluindo taxas.');

        $this->service->processTransaction($account, 'withdraw', 200, 'Teste de saldo insuficiente');
    }
}
