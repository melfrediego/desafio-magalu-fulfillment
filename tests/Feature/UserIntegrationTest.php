<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a listagem de usuários.
     */
    public function test_list_users(): void
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'users' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'name',
                                 'email',
                                 'created_at',
                                 'updated_at',
                             ]
                         ]
                     ]
                 ]);
    }

    /**
     * Testa a criação de um novo usuário.
     */
    public function test_create_user(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Usuário cadastrado com sucesso!',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    /**
     * Testa a exibição de um usuário específico.
     */
    public function test_show_user(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'user' => [
                         'id' => $user->id,
                         'name' => $user->name,
                         'email' => $user->email,
                     ]
                 ]);
    }

    /**
     * Testa a atualização de um usuário.
     */
    public function test_update_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'oldemail@example.com',
        ]);

        $updateData = [
            'name' => 'New Name',
            'email' => 'newemail@example.com',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Usuário editado com sucesso!',
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'newemail@example.com',
        ]);
    }

    /**
     * Testa a exclusão de um usuário.
     */
    public function test_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Usuário excluído com sucesso.',
                 ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Testa a criação de um usuário com validação falha.
     */
    public function test_create_user_fails_with_invalid_data(): void
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'status',
                     'errors' => [
                         'name',
                         'email',
                         'password',
                     ],
                 ]);
    }
}
