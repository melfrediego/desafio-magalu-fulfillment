<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de uma conta com sucesso.
     * Valida se a API cria corretamente uma conta quando todos os campos obrigatórios são fornecidos.
     */
    public function test_create_account_successfully()
    {
        $user = User::factory()->create(['is_client' => true]);
        $bank = Bank::factory()->create();

        $data = [
            'agency' => '1234',
            'number' => '56789',
            'balance' => 1000.00,
            'credit_limit' => 500.00,
            'user_id' => $user->id,
            'bank_id' => $bank->id,
        ];

        $response = $this->actingAs(User::factory()->create())->postJson('/api/accounts', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Conta cadastrada com sucesso!'
                 ]);

        // Verifica se a conta foi salva no banco de dados
        $this->assertDatabaseHas('accounts', $data);
    }

    /**
     * Testa a criação de uma conta com dados obrigatórios ausentes.
     * Valida se a API retorna erro 422 quando dados necessários estão ausentes.
     */
    public function test_create_account_with_missing_data()
    {
        $data = [
            'agency' => '', // Campo obrigatório ausente
            'number' => '56789',
        ];

        $response = $this->actingAs(User::factory()->create())->postJson('/api/accounts', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'errors' => ['agency', 'user_id', 'bank_id'], // Validação de campos obrigatórios
                 ]);
    }

    /**
     * Testa a atualização de uma conta existente com sucesso.
     * Valida se a API atualiza corretamente os dados de uma conta.
     */
    public function test_update_account_successfully()
    {
        $user = User::factory()->create(['is_client' => true]);
        $bank = Bank::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'bank_id' => $bank->id]);

        $data = [
            'agency' => '5678',
            'number' => '12345',
            'balance' => 2000.00,
            'credit_limit' => 1000.00,
            'user_id' => $user->id,
            'bank_id' => $bank->id,
        ];

        $response = $this->actingAs(User::factory()->create())->putJson("/api/accounts/{$account->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Conta atualizada com sucesso!'
                 ]);

        // Verifica se a conta foi atualizada no banco de dados
        $this->assertDatabaseHas('accounts', $data);
    }

    /**
     * Testa a tentativa de atualizar uma conta inexistente.
     * Valida se a API retorna erro 404 ao tentar atualizar uma conta que não existe.
     */
    public function test_update_non_existent_account()
    {
        $data = [
            'agency' => '5678',
            'number' => '12345',
        ];

        $response = $this->actingAs(User::factory()->create())->putJson('/api/accounts/9999', $data);

        $response->assertStatus(404)
                ->assertJson([
                    'status' => false,
                    'message' => 'Recurso não encontrado.',
                ]);
    }

    /**
     * Testa a exclusão de uma conta existente com sucesso.
     * Valida se a API remove corretamente uma conta existente.
     */
    public function test_delete_account_successfully()
    {
        $account = Account::factory()->create();

        $response = $this->actingAs(User::factory()->create())->deleteJson("/api/accounts/{$account->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Conta excluída com sucesso.'
                 ]);

        // Verifica se a conta foi removida do banco de dados
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    /**
     * Testa a tentativa de excluir uma conta inexistente.
     * Valida se a API retorna erro 404 ao tentar excluir uma conta que não existe.
     */
    public function test_delete_non_existent_account()
    {
        $response = $this->actingAs(User::factory()->create())
                        ->deleteJson('/api/accounts/9999');

        $response->assertStatus(404)
                ->assertJson([
                    'status' => false,
                    'message' => 'Recurso não encontrado.',
                ]);
    }

    /**
     * Testa a exibição de uma conta existente com sucesso.
     * Valida se a API retorna os detalhes de uma conta existente.
     */
    public function test_show_account_successfully()
    {
        $account = Account::factory()->create();

        $response = $this->actingAs(User::factory()->create())
                        ->getJson("/api/accounts/{$account->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'account' => [
                         'id' => $account->id,
                         'agency' => $account->agency,
                         'number' => $account->number,
                     ],
                 ]);
    }

    /**
     * Testa a tentativa de exibir uma conta inexistente.
     * Valida se a API retorna erro 404 ao tentar exibir uma conta que não existe.
     */
    public function test_show_non_existent_account()
    {
        $response = $this->actingAs(User::factory()->create())
                    ->getJson('/api/accounts/9999');

        $response->assertStatus(404)
                ->assertJson([
                    'status' => false,
                    'message' => 'Recurso não encontrado.', // Mensagem ajustada
                ]);
    }
}
