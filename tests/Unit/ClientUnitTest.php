<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a falha ao criar um cliente sem CPF/CNPJ.
     */
    public function test_fail_create_client_without_cpf_cnpj()
    {
        $this->expectException(\App\Exceptions\BusinessValidationException::class);
        $this->expectExceptionMessage('O campo cpf_cnpj é obrigatório.');

        User::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => bcrypt('password123'),
            'person_type' => 'PF',
            'is_client' => true, // Marca como cliente
        ]);
    }

    /**
     * Testa a falha ao criar um cliente com CPF/CNPJ duplicado.
     */
    public function test_fail_create_client_with_duplicate_cpf_cnpj()
    {
        // Primeiro cliente com CPF/CNPJ
        User::factory()->create([
            'cpf_cnpj' => '12345678900', // CPF/CNPJ válido
            'person_type' => 'PF',
            'is_client' => true, // Cliente
        ]);

        // Tentativa de criar um cliente com o mesmo CPF/CNPJ
        $this->expectException(\App\Exceptions\BusinessValidationException::class);
        $this->expectExceptionMessage('O CPF/CNPJ já está em uso.');

        User::create([
            'name' => 'Outro Cliente',
            'email' => 'outro@teste.com',
            'password' => bcrypt('password123'),
            'cpf_cnpj' => '12345678900', // CPF/CNPJ duplicado
            'person_type' => 'PF',
            'is_client' => true, // Cliente
        ]);
    }

    /**
     * Testa a criação de um cliente válido com CPF/CNPJ.
     */
    public function test_create_valid_client()
    {
        $user = User::factory()->create([
            'name' => 'Cliente Válido',
            'email' => 'valid@client.com',
            'password' => bcrypt('password123'),
            'cpf_cnpj' => '98765432100', // CPF/CNPJ válido
            'person_type' => 'PF',
            'is_client' => true, // Cliente
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'valid@client.com',
            'cpf_cnpj' => '98765432100',
            'is_client' => true,
        ]);
    }

    /**
     * Testa a criação de um usuário não cliente sem CPF/CNPJ.
     */
    public function test_create_non_client_without_cpf_cnpj()
    {
        $user = User::factory()->create([
            'name' => 'Usuário Não Cliente',
            'email' => 'nonclient@test.com',
            'password' => bcrypt('password123'),
            'cpf_cnpj' => null, // CPF/CNPJ não obrigatório
            'person_type' => 'PF',
            'is_client' => false, // Não é cliente
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'nonclient@test.com',
            'is_client' => false,
        ]);
    }
}
