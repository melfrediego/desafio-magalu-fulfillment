<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use App\Models\Bank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de uma conta válida.
     */
    public function test_create_valid_account()
    {
        // Cria um usuário com CPF/CNPJ válido
        $user = User::factory()->create([
            'cpf_cnpj' => '12345678900', // CPF fixo para consistência nos testes
        ]);

        // Cria um banco
        $bank = Bank::factory()->create();

        // Cria uma conta associada ao usuário e banco
        $account = Account::factory()->create([
            'agency' => '1234',
            'number' => '56789',
            'balance' => 1000.00,
            'credit_limit' => 500.00,
            'user_id' => $user->id,
            'bank_id' => $bank->id,
        ]);

        // Verifica se a conta foi criada corretamente no banco de dados
        $this->assertDatabaseHas('accounts', [
            'agency' => '1234',
            'number' => '56789',
            'user_id' => $user->id,
            'bank_id' => $bank->id,
        ]);
    }

    /**
     * Testa falha na criação de uma conta sem informar a agência.
     */
    public function test_fail_create_account_without_agency()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Cria uma conta sem a agência (agency)
        Account::create([
            'number' => '56789',
            'balance' => 1000.00,
            'credit_limit' => 500.00,
            'user_id' => User::factory()->create(['cpf_cnpj' => '12345678900'])->id,
            'bank_id' => Bank::factory()->create()->id,
        ]);
    }

    /**
     * Testa falha ao criar uma conta sem associar a um usuário.
     */
    public function test_fail_create_account_without_user()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Account::create([
            'agency' => '1234',
            'number' => '56789',
            'balance' => 1000.00,
            'credit_limit' => 500.00,
            'bank_id' => Bank::factory()->create()->id,
        ]);
    }

    /**
     * Testa falha ao criar uma conta sem associar a um banco.
     */
    public function test_fail_create_account_without_bank()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Account::create([
            'agency' => '1234',
            'number' => '56789',
            'balance' => 1000.00,
            'credit_limit' => 500.00,
            'user_id' => User::factory()->create(['cpf_cnpj' => '12345678900'])->id,
        ]);
    }
}
