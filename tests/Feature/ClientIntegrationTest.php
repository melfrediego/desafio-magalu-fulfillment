<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_clients_successfully()
    {
        User::factory()->count(5)->create(['is_client' => true]);
        $response = $this->actingAs(User::factory()->create())->getJson('/api/clients');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'clients' => [
                         'data' => [['id', 'name', 'email', 'cpf_cnpj', 'person_type']],
                     ],
                 ]);
    }

    public function test_create_client_successfully()
    {
        $data = [
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'cpf_cnpj' => '12345678900',
            'person_type' => 'PF',
        ];

        $response = $this->actingAs(User::factory()->create())->postJson('/api/clients', $data);

        $response->assertStatus(201)
                 ->assertJson(['status' => true, 'message' => 'Cliente cadastrado com sucesso!']);
    }

    public function test_fail_create_client_with_duplicate_cpf_cnpj()
    {
        User::factory()->create(['cpf_cnpj' => '12345678900']);
        $data = ['cpf_cnpj' => '12345678900', 'person_type' => 'PF'];

        $response = $this->actingAs(User::factory()->create())->postJson('/api/clients', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure(['status', 'errors']);
    }

    public function test_delete_client_successfully()
    {
        $client = User::factory()->create(['is_client' => true]);
        $response = $this->actingAs(User::factory()->create())->deleteJson("/api/clients/{$client->id}");

        $response->assertStatus(200)
                 ->assertJson(['status' => true, 'message' => 'Cliente excluído com sucesso.']);
    }
}
