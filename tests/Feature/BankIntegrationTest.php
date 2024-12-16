<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Bank;
use App\Models\User;

class BankControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa o endpoint para listar todos os bancos.
     */
    public function test_it_lists_all_banks()
    {
        Bank::factory()->count(5)->create();

        $response = $this->actingAs(User::factory()->create())->getJson('/api/banks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'banks' => [
                         'data',
                         'links',
                         'meta',
                     ],
                 ]);
    }

    /**
     * Testa a criação de um novo banco com sucesso.
     */
    public function test_it_creates_a_new_bank()
    {
        $bankData = [
            'code' => '001',
            'name' => 'Banco Teste',
        ];

        $response = $this->actingAs(User::factory()->create())->postJson('/api/banks', $bankData);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Banco cadastrado com sucesso!',
                 ]);

        $this->assertDatabaseHas('banks', $bankData);
    }

    /**
     * Testa a validação ao tentar criar um banco com dados inválidos.
     */
    public function test_it_fails_to_create_a_bank_with_invalid_data()
    {
        $response = $this->actingAs(User::factory()->create())->postJson('/api/banks', []);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => [
                         'code',
                         'name',
                     ],
                 ]);
    }

    /**
     * Testa a exibição de um banco específico.
     */
    public function test_it_shows_a_specific_bank()
    {
        $bank = Bank::factory()->create();

        $response = $this->actingAs(User::factory()->create())->getJson("/api/banks/{$bank->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'bank' => [
                         'id' => $bank->id,
                         'code' => $bank->code,
                         'name' => $bank->name,
                     ],
                 ]);
    }

    /**
     * Testa a exibição de um banco não existente.
     */
    public function test_it_returns_not_found_for_nonexistent_bank()
    {
        $response = $this->actingAs(User::factory()->create())->getJson('/api/banks/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Banco não encontrado.',
                 ]);
    }

    /**
     * Testa a atualização de um banco com sucesso.
     */
    public function test_it_updates_a_bank_successfully()
    {
        $bank = Bank::factory()->create();

        $updatedData = [
            'code' => '002',
            'name' => 'Banco Atualizado',
        ];

        $response = $this->actingAs(User::factory()->create())->putJson("/api/banks/{$bank->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Banco atualizado com sucesso!',
                 ]);

        $this->assertDatabaseHas('banks', $updatedData);
    }

    /**
     * Testa a validação ao tentar atualizar um banco com dados inválidos.
     */
    public function test_it_fails_to_update_a_bank_with_invalid_data()
    {
        $bank = Bank::factory()->create();

        $response = $this->actingAs(User::factory()->create())->putJson("/api/banks/{$bank->id}", []);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => [
                         'code',
                         'name',
                     ],
                 ]);
    }

    /**
     * Testa a exclusão de um banco com sucesso.
     */
    public function test_it_deletes_a_bank_successfully()
    {
        $bank = Bank::factory()->create();

        $response = $this->actingAs(User::factory()->create())->deleteJson("/api/banks/{$bank->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Banco excluído com sucesso.',
                 ]);

        $this->assertDatabaseMissing('banks', ['id' => $bank->id]);
    }

    /**
     * Testa a exclusão de um banco não existente.
     */
    public function test_it_returns_error_when_deleting_nonexistent_bank()
    {
        $response = $this->actingAs(User::factory()->create())->deleteJson('/api/banks/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Banco não encontrado.',
                 ]);
    }
}
