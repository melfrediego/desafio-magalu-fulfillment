<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\PendingTransaction;
use App\Models\Transaction;
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
     * Testa a operação de transferência entre contas.
     */
    public function test_transfer_updates_both_accounts_correctly(): void
    {
        $sourceAccount = Account::factory()->create(['balance' => 1000]);
        $targetAccount = Account::factory()->create(['balance' => 500]);

        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $sourceAccount->id,
            'target_account_id' => $targetAccount->id,
            'amount' => 300,
            'type' => 'transfer',
            'description' => 'Transferência teste',
            'processed' => false,
        ]);

        $this->service->processPendingTransaction($pendingTransaction);

        $this->assertEquals(700, $sourceAccount->fresh()->balance); // Fonte
        $this->assertEquals(800, $targetAccount->fresh()->balance); // Destino
        $this->assertTrue($pendingTransaction->fresh()->processed);
    }

    /**
     * Testa saque com saldo insuficiente.
     */
    public function test_withdraw_fails_on_insufficient_balance(): void
    {
        $account = Account::factory()->create(['balance' => 100]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Saldo insuficiente.');

        $this->service->createTransactionRecord([
            'account_id' => $account->id,
            'type' => 'withdraw',
            'amount' => 200,
            'description' => 'Saque inválido',
        ]);
    }

    /**
     * Testa reprocessamento de transações pendentes.
     */
    public function test_reprocess_pending_transactions(): void
    {
        $sourceAccount = Account::factory()->create(['balance' => 1000]);
        $targetAccount = Account::factory()->create(['balance' => 500]);

        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $sourceAccount->id,
            'target_account_id' => $targetAccount->id,
            'amount' => 300,
            'type' => 'transfer',
            'description' => 'Transferência teste',
            'processed' => false,
        ]);

        $this->service->reprocessPendingTransactions();

        $this->assertTrue($pendingTransaction->fresh()->processed);
        $this->assertEquals(700, $sourceAccount->fresh()->balance);
        $this->assertEquals(800, $targetAccount->fresh()->balance);
    }
}
