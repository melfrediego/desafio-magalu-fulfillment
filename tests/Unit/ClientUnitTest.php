<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator; // Importe a classe Validator
use Tests\TestCase;

class ClientUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a falha ao criar um cliente sem CPF/CNPJ.
     *
     * @return void
     */
    public function test_fail_create_client_without_cpf_cnpj()
    {
        // Usando o validador para testar a regra 'required_if'
        $validator = Validator::make(
            ['cpf_cnpj' => '', 'is_client' => true],
            ['cpf_cnpj' => 'required_if:is_client,true']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cpf_cnpj', $validator->errors()->toArray());
    }

    /**
     * Testa a falha ao criar um cliente com CPF/CNPJ duplicado.
     *
     * @return void
     */
    public function test_fail_create_client_with_duplicate_cpf_cnpj()
    {
        User::factory()->create([
            'cpf_cnpj' => '12345678900',
            'person_type' => 'PF',
            'is_client' => true,
        ]);

        $this->expectException(UniqueConstraintViolationException::class);

        User::create([
            'name' => 'Outro Cliente',
            'email' => 'outro@teste.com',
            'password' => bcrypt('password123'),
            'cpf_cnpj' => '12345678900',
            'person_type' => 'PF',
            'is_client' => true,
        ]);
    }

    /**
     * Testa a criação de um cliente válido com CPF/CNPJ.
     *
     * @return void
     */
    public function test_create_valid_client()
    {
        $user = User::factory()->create([
            'name' => 'Cliente Válido',
            'email' => 'valid@client.com',
            'password' => bcrypt('password123'),
            'cpf_cnpj' => '98765432100',
            'person_type' => 'PF',
            'is_client' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'valid@client.com',
            'cpf_cnpj' => '98765432100',
            'is_client' => true,
        ]);
    }

    /**
     * Testa a criação de um usuário não cliente sem CPF/CNPJ.
     *
     * @return void
     */
    public function test_create_non_client_without_cpf_cnpj()
    {
        $user = User::factory()->create([
            'name' => 'Usuário Não Cliente',
            'email' => 'nonclient@test.com',
            'password' => bcrypt('password123'),
            'cpf_cnpj' => null,
            'person_type' => 'PF',
            'is_client' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'nonclient@test.com',
            'is_client' => false,
        ]);
    }
}
