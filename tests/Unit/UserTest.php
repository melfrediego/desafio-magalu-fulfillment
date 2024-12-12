<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase; // Certifique-se de que está usando o TestCase correto do Laravel

class UserTest extends TestCase
{
    use RefreshDatabase; // Garante que o banco de dados de testes seja reiniciado a cada teste

    /**
     * Testa a criação de um usuário válido.
     * Verifica se o usuário foi criado com os dados corretos.
     */
    public function test_create_valid_user()
    {
        $user = User::factory()->create([
            'name' => 'João Teste',
            'email' => 'joao@teste.com',
            'password' => bcrypt('password123'), // Use bcrypt ou Hash::make
            'cpf_cnpj' => '12345678900', // CPF válido
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@teste.com',
            'cpf_cnpj' => '12345678900',
        ]);
    }

    /**
     * Testa falha ao criar um usuário sem e-mail.
     * O Laravel lança uma exceção devido à validação do campo obrigatório.
     */
    public function test_fail_create_user_without_email()
    {
        $this->expectException(\App\Exceptions\BusinessValidationException::class);
        $this->expectExceptionMessage('O campo cpf_cnpj é obrigatório.');

            User::create([
                'name' => 'João Teste',
                'cpf_cnpj' => '12345678900',
                'password' => bcrypt('password123'),
            ]);
        }
}
