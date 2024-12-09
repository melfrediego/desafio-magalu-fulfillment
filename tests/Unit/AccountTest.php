<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Bank;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    /**
     * Testa a criação de uma conta válida.
     * Verifica se a conta foi salva corretamente no banco de dados.
     */
    public function test_create_valid_account()
    {
        $user = User::factory()->create();
        $bank = Bank::factory()->create();

        $account = Account::factory()->create([
            'agency' => '1234',
            'number' => '56789',
            'balance' => 1000.00,
            'credit_limit' => 500.00,
            'user_id' => $user->id,
            'bank_id' => $bank->id,
        ]);

        $this->assertDatabaseHas('accounts', [
            'agency' => '1234',
            'number' => '56789',
        ]);
    }

    /**
     * Testa falha ao criar uma conta sem agência.
     * O Laravel lança uma exceção devido à validação de campos obrigatórios.
     */
    public function test_fail_create_account_without_agency()
    {
        $user = User::factory()->create();
        $bank = Bank::factory()->create();

        $this->expectException(\Illuminate\Database\QueryException::class);

        Account::create([
            'number' => '56789',
            'balance' => 1000.00,
            'credit_limit' => 500.00,
            'user_id' => $user->id,
            'bank_id' => $bank->id,
        ]);
    }
}
