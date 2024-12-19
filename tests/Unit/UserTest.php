<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de um usuário válido.
     */
    public function test_create_valid_user()
    {
        $user = User::factory()->create([
            'name' => 'João Teste',
            'email' => 'joao@teste.com',
            'password' => bcrypt('password123'),
            'cpf_cnpj' => '12345678900',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => 'joao@teste.com',
            'cpf_cnpj' => '12345678900',
        ]);
    }

    /**
     * Testa a validação do campo email como obrigatório.
     */
    public function test_email_is_required()
    {
        $validator = Validator::make(
            ['email' => ''],
            ['email' => 'required|email']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /**
     * Testa a validação do formato do email.
     */
    public function test_email_must_be_valid_email()
    {
        $validator = Validator::make(
            ['email' => 'email-invalido'],
            ['email' => 'required|email']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /**
     * Testa a validação do CPF/CNPJ como obrigatório para clientes.
     */
    public function test_cpf_cnpj_is_required_for_clients()
    {
        $validator = Validator::make(
            ['cpf_cnpj' => '', 'is_client' => true],
            ['cpf_cnpj' => 'required_if:is_client,true']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cpf_cnpj', $validator->errors()->toArray());
    }

    /**
     * Testa a validação do CPF/CNPJ como único.
     */
    public function test_cpf_cnpj_must_be_unique()
    {
        User::factory()->create(['cpf_cnpj' => '12345678900']);

        $validator = Validator::make(
            ['cpf_cnpj' => '12345678900'],
            ['cpf_cnpj' => 'unique:users,cpf_cnpj']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('cpf_cnpj', $validator->errors()->toArray());
    }

    /**
     * Testa a validação do email como único.
     */
    public function test_email_must_be_unique()
    {
        User::factory()->create(['email' => 'joao@teste.com']);

        $validator = Validator::make(
            ['email' => 'joao@teste.com'],
            ['email' => 'unique:users,email']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }
}
