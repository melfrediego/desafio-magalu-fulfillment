<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Account;


class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o endpoint de depósito.
     */
    public function test_deposit_endpoint_works_correctly()
    {
        // Configura conta inicial com saldo de 1000
        $account = Account::factory()->create(['balance' => 1000]);

        // Envia requisição para o endpoint de depósito
        $response = $this->postJson('/api/transactions/deposit', [
            'account_id' => $account->id,
            'amount' => 500,
            'description' => 'Teste de depósito',
        ]);

        // Verifica se a resposta foi bem-sucedida
        $response->assertStatus(200);

        // Verifica se o saldo foi atualizado
        $this->assertEquals(1500, $account->fresh()->balance);
    }

    /**
     * Testa o endpoint de saque com saldo insuficiente.
     */
    public function test_withdraw_endpoint_handles_insufficient_balance()
    {
        // Configura conta inicial com saldo insuficiente
        $account = Account::factory()->create(['balance' => 100]);

        // Envia requisição para o endpoint de saque
        $response = $this->postJson('/api/transactions/withdraw', [
            'account_id' => $account->id,
            'amount' => 200,
            'description' => 'Teste de saque',
        ]);

        // Verifica se a resposta retornou erro
        $response->assertStatus(400);

        // Verifica se o saldo permaneceu inalterado
        $this->assertEquals(100, $account->fresh()->balance);
    }

    /**
     * Testa o endpoint de transferência.
     */
    public function test_transfer_endpoint_works_correctly()
    {
        // Configura contas de origem e destino
        $sourceAccount = Account::factory()->create(['balance' => 1000]);
        $targetAccount = Account::factory()->create(['balance' => 500]);

        // Envia requisição para o endpoint de transferência
        $response = $this->postJson('/api/transactions/transfer', [
            'source_account_id' => $sourceAccount->id,
            'target_account_id' => $targetAccount->id,
            'amount' => 300,
            'description' => 'Teste de transferência',
        ]);

        // Verifica se a resposta foi bem-sucedida
        $response->assertStatus(200);

        // Verifica os saldos atualizados
        $this->assertEquals(694, $sourceAccount->fresh()->balance); // 300 + 2% fee
        $this->assertEquals(800, $targetAccount->fresh()->balance);
    }
}
