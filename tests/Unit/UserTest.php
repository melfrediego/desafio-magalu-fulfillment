<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * Testa a criação de um usuário válido.
     * Verifica se o usuário foi criado com os dados corretos.
     */
    public function test_create_valid_user()
    {
        $user = User::factory()->create([
            'name' => 'João Teste',
            'email' => 'joao@teste.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@teste.com',
        ]);
    }

    /**
     * Testa falha ao criar um usuário sem e-mail.
     * O Laravel lança uma exceção devido à validação do campo obrigatório.
     */
    public function test_fail_create_user_without_email()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'name' => 'João Teste',
            'password' => bcrypt('password123'),
        ]);
    }
}
