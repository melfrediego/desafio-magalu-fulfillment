<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a listagem de clientes com sucesso.
     */
    public function test_list_clients_successfully()
    {
        User::factory(5)->create(['is_client' => true]);

        $response = $this->getJson('/api/clients');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'clients' => [
                         'data' => [['id', 'name', 'email', 'cpf_cnpj', 'person_type']],
                     ],
                 ]);
    }

    /**
     * Testa a criação de um cliente com sucesso.
     */
    public function test_create_client_successfully()
    {
        $data = [
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_whatsapp' => '11999999999',
            'birth_date' => '1990-01-01',
            'cpf_cnpj' => '12345678900',
            'person_type' => 'PF',
        ];

        $response = $this->postJson('/api/clients', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Cliente cadastrado com sucesso!',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'cliente@teste.com',
            'cpf_cnpj' => '12345678900',
        ]);
    }

    /**
     * Testa a criação de um cliente com CPF/CNPJ duplicado.
     */
    public function test_fail_create_client_with_duplicate_cpf_cnpj()
    {
        User::factory()->create(['cpf_cnpj' => '12345678900', 'person_type' => 'PF']);

        $data = [
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cpf_cnpj' => '12345678900',
            'person_type' => 'PF',
        ];

        $response = $this->postJson('/api/clients', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure(['status', 'errors']);
    }

    /**
     * Testa a atualização de um cliente com sucesso.
     */
    public function test_update_client_successfully()
    {
        $client = User::factory()->create(['is_client' => true]);

        $data = [
            'name' => 'Cliente Atualizado',
            'email' => 'clienteatualizado@teste.com',
            'phone_whatsapp' => '11988888888',
        ];

        $response = $this->putJson("/api/clients/{$client->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Cliente atualizado com sucesso!',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $client->id,
            'name' => 'Cliente Atualizado',
            'email' => 'clienteatualizado@teste.com',
        ]);
    }

    /**
     * Testa a exclusão de um cliente com sucesso.
     */
    public function test_delete_client_successfully()
    {
        $client = User::factory()->create(['is_client' => true]);

        $response = $this->deleteJson("/api/clients/{$client->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Cliente excluído com sucesso.',
                 ]);

        $this->assertDatabaseMissing('users', ['id' => $client->id]);
    }

    /**
     * Testa a exclusão de um cliente inexistente.
     */
    public function test_fail_delete_non_existent_client()
    {
        $response = $this->deleteJson('/api/clients/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'status' => false,
                     'message' => 'Usuário não é um cliente.',
                 ]);
    }
}
