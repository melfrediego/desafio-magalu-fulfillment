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
            'is_client' => true,
        ]);
    }

    /**
     * Testa a falha ao criar um cliente com CPF/CNPJ duplicado.
     */
    public function test_fail_create_client_with_duplicate_cpf_cnpj()
    {
        User::factory()->create([
            'cpf_cnpj' => '12345678900',
            'person_type' => 'PF',
        ]);

        $this->expectException(\App\Exceptions\BusinessValidationException::class);
        $this->expectExceptionMessage('O CPF/CNPJ já está em uso.');

        User::create([
            'name' => 'Outro Cliente',
            'email' => 'outro@teste.com',
            'password' => bcrypt('password123'),
            'cpf_cnpj' => '12345678900',
            'person_type' => 'PF',
            'is_client' => true,
        ]);
    }
}
