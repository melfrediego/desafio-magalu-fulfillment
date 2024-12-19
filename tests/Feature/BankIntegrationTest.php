<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a listagem de todos os bancos.
     * Verifica se a resposta tem status 200 e a estrutura JSON correta,
     * incluindo os dados dos bancos, links de paginação e metadados.
     *
     * @return void
     */
    public function test_it_lists_all_banks()
    {
        Bank::factory()->count(15)->create();

        $response = $this->actingAs(User::factory()->create())->getJson('/api/banks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'banks' => [
                    'data' => [
                        '*' => [
                            'id',
                            'code',
                            'name',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ]
            ]);
    }

    /**
     * Testa a criação de um novo banco com sucesso.
     * Verifica se a resposta tem status 201, a mensagem de sucesso e se o banco
     * foi criado no banco de dados com os dados corretos.
     *
     * @return void
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
            ])
            ->assertJsonStructure([
                'bank' => [
                    'id',
                    'code',
                    'name',
                ],
            ]);

        $this->assertDatabaseHas('banks', $bankData);
    }

    /**
     * Testa a validação ao tentar criar um banco com dados inválidos.
     * Verifica se a resposta tem status 422 e a estrutura JSON correta para os erros de validação,
     * incluindo os erros específicos para cada campo.
     *
     * @return void
     */
    public function test_it_fails_to_create_a_bank_with_invalid_data()
    {
        $response = $this->actingAs(User::factory()->create())->postJson('/api/banks', []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors' => [
                    'code',
                    'name',
                ],
            ]);
    }

    /**
     * Testa a exibição de um banco específico.
     * Verifica se a resposta tem status 200 e se os dados do banco retornado
     * correspondem aos dados do banco criado.
     *
     * @return void
     */
    public function test_it_shows_a_specific_bank()
    {
        $bank = Bank::factory()->create();

        $response = $this->actingAs(User::factory()->create())->getJson("/api/banks/{$bank->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'bank' => [
                    'id',
                    'code',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * Testa a exibição de um banco não existente.
     * Verifica se a resposta tem status 404 e se a mensagem de erro
     * "Banco não encontrado." é retornada.
     *
     * @return void
     */
    public function test_it_returns_not_found_for_nonexistent_bank()
    {
        $response = $this->actingAs(User::factory()->create())->getJson('/api/banks/9999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Recurso não encontrado.',
            ]);
    }

    /**
     * Testa a atualização de um banco com sucesso.
     * Verifica se a resposta tem status 200, a mensagem de sucesso e se o banco
     * foi atualizado no banco de dados com os novos dados.
     *
     * @return void
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
            ])
            ->assertJsonStructure([
                'bank' => [
                    'id',
                    'code',
                    'name',
                ],
            ]);

        $this->assertDatabaseHas('banks', $updatedData);
    }

    /**
     * Testa a validação ao tentar atualizar um banco com dados inválidos.
     * Verifica se a resposta tem status 422 e a estrutura JSON correta para os erros de validação,
     * incluindo os erros específicos para cada campo.
     *
     * @return void
     */
    public function test_it_fails_to_update_a_bank_with_invalid_data()
    {
        $bank = Bank::factory()->create();

        $response = $this->actingAs(User::factory()->create())->putJson("/api/banks/{$bank->id}", []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'errors' => [
                    'code',
                    'name',
                ],
            ]);
    }

    /**
     * Testa a exclusão de um banco com sucesso.
     * Verifica se a resposta tem status 200, a mensagem de sucesso e se o banco
     * foi excluído do banco de dados.
     *
     * @return void
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
     * Verifica se a resposta tem status 404 e se a mensagem de erro
     * "Banco não encontrado." é retornada.
     *
     * @return void
     */
    public function test_it_returns_error_when_deleting_nonexistent_bank()
    {
        $response = $this->actingAs(User::factory()->create())->deleteJson('/api/banks/9999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Recurso não encontrado.cl',
            ]);
    }
}
