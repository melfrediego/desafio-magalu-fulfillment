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

    public function test_create_account_successfully()
    {
        $user = User::factory()->create();
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
                     'message' => 'Conta criada com sucesso!'
                 ]);

        $this->assertDatabaseHas('accounts', $data);
    }

    public function test_create_account_with_missing_data()
    {
        $data = [
            'agency' => '', // Faltando dados obrigatórios
            'number' => '56789',
        ];

        $response = $this->actingAs(User::factory()->create())->postJson('/api/accounts', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'errors' => ['agency', 'user_id', 'bank_id'],
                 ]);
    }

    public function test_update_account_successfully()
    {
        $account = Account::factory()->create();

        $data = [
            'agency' => '5678',
            'number' => '12345',
            'balance' => 2000.00,
            'credit_limit' => 1000.00,
        ];

        $response = $this->actingAs(User::factory()->create())->putJson("/api/accounts/{$account->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Conta atualizada com sucesso!'
                 ]);

        $this->assertDatabaseHas('accounts', $data);
    }

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
                     'message' => 'Conta não encontrada.',
                 ]);
    }

    public function test_delete_account_successfully()
    {
        $account = Account::factory()->create();

        $response = $this->actingAs(User::factory()->create())->deleteJson("/api/accounts/{$account->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Conta excluída com sucesso.'
                 ]);

        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    public function test_delete_non_existent_account()
    {
        $response = $this->actingAs(User::factory()->create())->deleteJson('/api/accounts/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Conta não encontrada.',
                 ]);
    }
}
