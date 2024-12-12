<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Account;
use App\Models\User;
use App\Models\PendingTransaction;
use App\Models\Transaction;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

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
                     'errors' => [
                         'account_id' => [
                             'The selected account id is invalid.'
                         ]
                     ]
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
                     'errors' => [
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
        $account = Account::factory()->create(['balance' => 1000]);

        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 200,
            'type' => 'deposit',
            'processed' => false,
        ]);

        $response = $this->actingAs(User::factory()->create())->postJson('/api/transactions/reprocess');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Transações pendentes reprocessadas.',
                 ]);

        $this->assertTrue($pendingTransaction->fresh()->processed);
        $this->assertEquals(1200, $account->fresh()->balance);
    }

    /**
     * Testa a recuperação do status de uma transação com sucesso.
     */
    public function test_get_transaction_status()
    {
        $transaction = Transaction::factory()->create([
            'status' => 'success',
        ]);

        $response = $this->actingAs(User::factory()->create())->getJson("/api/transactions/{$transaction->id}/status");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'transaction' => [
                         'id' => $transaction->id,
                         'status' => 'success',
                     ],
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
