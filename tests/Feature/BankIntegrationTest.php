<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Bank;
use App\Models\User;

class BankIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_bank_successfully()
    {
        $data = [
            'name' => 'Bank Test',
            'code' => '1234',
        ];

        $response = $this->actingAs(User::factory()->create())->postJson('/api/banks', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Banco criado com sucesso!'
                 ]);

        $this->assertDatabaseHas('banks', $data);
    }

    public function test_create_bank_with_missing_data()
    {
        $data = ['name' => '']; // Faltando o campo "code"

        $response = $this->actingAs(User::factory()->create())->postJson('/api/banks', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'errors' => ['name', 'code'],
                 ]);
    }

    public function test_update_bank_successfully()
    {
        $bank = Bank::factory()->create();

        $data = [
            'name' => 'Updated Bank',
            'code' => '5678',
        ];

        $response = $this->actingAs(User::factory()->create())->putJson("/api/banks/{$bank->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Banco atualizado com sucesso!'
                 ]);

        $this->assertDatabaseHas('banks', $data);
    }

    public function test_update_non_existent_bank()
    {
        $data = [
            'name' => 'Non-existent Bank',
            'code' => '9999',
        ];

        $response = $this->actingAs(User::factory()->create())->putJson('/api/banks/9999', $data);

        $response->assertStatus(404)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Banco não encontrado.',
                 ]);
    }

    public function test_delete_bank_successfully()
    {
        $bank = Bank::factory()->create();

        $response = $this->actingAs(User::factory()->create())->deleteJson("/api/banks/{$bank->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Banco excluído com sucesso.'
                 ]);

        $this->assertDatabaseMissing('banks', ['id' => $bank->id]);
    }

    public function test_delete_non_existent_bank()
    {
        $response = $this->actingAs(User::factory()->create())->deleteJson('/api/banks/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Banco não encontrado.',
                 ]);
    }
}
