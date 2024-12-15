<?php

namespace Tests\Feature;

use App\Jobs\ReprocessPendingTransactionsJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Account;
use App\Models\User;
use App\Models\PendingTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\Queue;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configurações iniciais antes de cada teste.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Define a fila para ser processada de forma fake
        Queue::fake();
    }

    /**
     * Testa o endpoint de depósito com sucesso.
     */
    public function test_deposit_endpoint_works_correctly()
    {
        $account = Account::factory()->create(['balance' => 1000]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/transactions/deposit', [
            'account_id' => $account->id,
            'amount' => 500,
            'description' => 'Depósito teste',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'transaction' => [
                         'account_id', 'type', 'amount', 'description', 'status', 'created_at', 'updated_at',
                     ],
                 ]);

        // Log::info("message");

        $this->assertEquals(1500, $account->fresh()->balance);
    }

    /**
     * Testa o endpoint de depósito com conta inválida.
     */
    public function test_deposit_endpoint_fails_with_invalid_account()
    {
        $response = $this->actingAs(User::factory()->create())->postJson('/api/transactions/deposit', [
            'account_id' => 9999,
            'amount' => 500,
            'description' => 'Depósito inválido',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Erro de validação.',
                     'erros' => [
                         'account_id' => [
                             'The selected account id is invalid.',
                         ],
                     ],
                 ]);
    }

    /**
     * Testa o endpoint de saque com sucesso.
     */
    public function test_withdraw_endpoint_works_correctly()
    {
        $account = Account::factory()->create(['balance' => 1000]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/transactions/withdraw', [
            'account_id' => $account->id,
            'amount' => 200,
            'description' => 'Saque teste',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'transaction' => [
                         'account_id', 'type', 'amount', 'description', 'status', 'created_at', 'updated_at',
                     ],
                 ]);

        $this->assertEquals(800, $account->fresh()->balance);
    }

    /**
     * Testa o endpoint de saque com saldo insuficiente.
     */
    public function test_withdraw_endpoint_fails_with_insufficient_balance()
    {
        $account = Account::factory()->create(['balance' => 100]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/transactions/withdraw', [
            'account_id' => $account->id,
            'amount' => 200,
            'description' => 'Saque com saldo insuficiente',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'Erro ao realizar saque.',
                     'error' => 'Saldo insuficiente.',
                 ]);

        $this->assertEquals(100, $account->fresh()->balance);
    }

    /**
     * Testa o endpoint de transferência com sucesso.
     */
    public function test_transfer_endpoint_works_correctly()
    {
        $sourceAccount = Account::factory()->create(['balance' => 1000]);
        $targetAccount = Account::factory()->create(['balance' => 500]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/transactions/transfer', [
            'source_account_id' => $sourceAccount->id,
            'target_account_id' => $targetAccount->id,
            'amount' => 300,
            'description' => 'Transferência teste',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'transaction' => [
                        'account_id', 'type', 'amount', 'description', 'status', 'created_at', 'updated_at',
                    ],
                ]);

        $this->assertEquals(700, $sourceAccount->fresh()->balance);
        $this->assertEquals(800, $targetAccount->fresh()->balance);
    }

    /**
     * Testa o endpoint de transferência com conta destino inválida.
     */
    public function test_transfer_endpoint_fails_with_invalid_target_account()
    {
        $sourceAccount = Account::factory()->create(['balance' => 1000]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/transactions/transfer', [
            'source_account_id' => $sourceAccount->id,
            'target_account_id' => 9999,
            'amount' => 300,
            'description' => 'Transferência com conta destino inválida',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Erro de validação.',
                     'erros' => [
                         'target_account_id' => [
                             'The selected target account id is invalid.'
                         ]
                     ]
                 ]);

        $this->assertEquals(1000, $sourceAccount->fresh()->balance);
    }

    /**
     * Testa o reprocessamento de transações pendentes.
     */
    public function test_reprocess_pending_transactions()
    {
        // Criação de uma transação pendente
        $account = Account::factory()->create(['balance' => 1000]);
        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 200,
            'type' => 'deposit',
            'processed' => false,
        ]);

        // Simula a requisição para reprocessar
        $response = $this->actingAs(User::factory()->create())
                        ->postJson('/api/transactions/reprocess');

        $response->assertStatus(202)
                ->assertJson([
                    'message' => 'Reprocessamento de transações pendentes enviado para a fila.',
                ]);

        // Verifica se o Job foi enfileirado
        Queue::assertPushed(ReprocessPendingTransactionsJob::class);

        // Não verifica o estado de 'processed' porque será atualizado em segundo plano
        $this->assertDatabaseHas('pending_transactions', [
            'id' => $pendingTransaction->id,
            'processed' => false, // Confirma que ainda não foi processado
    ]);
    }

    /**
     * Testa a recuperação do status de uma transação com sucesso.
     */
    public function test_get_transaction_status()
    {
        $transaction = PendingTransaction::factory()->create([
            'processed' => true,
        ]);

        $response = $this->actingAs(User::factory()->create())->getJson("/api/transactions/{$transaction->transaction_id}/status");

        $response->assertStatus(200)
                 ->assertJson([
                     'transaction_id' => $transaction->transaction_id,
                     'status' => 'processed',
                     'type' => $transaction->type,
                 ]);
    }

    /**
     * Testa a recuperação do status de uma transação com ID inválido.
     */
    public function test_get_transaction_status_fails_with_invalid_id()
    {
        $response = $this->actingAs(User::factory()->create())->getJson('/api/transactions/9999/status');

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Transação não encontrada.',
                 ]);
    }
}
