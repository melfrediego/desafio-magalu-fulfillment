<?php

namespace Tests\Unit;

use App\Models\Bank;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class BankTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de um banco válido.
     * Confirma se o banco foi criado e se está presente no banco de dados.
     */
    public function test_create_valid_bank()
    {
        $bank = Bank::factory()->create([
            'code' => '123456',
            'name' => 'Banco Teste',
        ]);

        $this->assertDatabaseHas('banks', [
            'code' => '123456',
            'name' => 'Banco Teste',
        ]);
    }

    /**
     * Testa a falha na criação de um banco sem código.
     * O Laravel lança uma exceção por não atender aos requisitos de validação.
     */
    public function test_fail_create_bank_without_code()
    {
        $this->expectException(QueryException::class);

        Bank::create([
            'name' => 'Banco Teste',
        ]);
    }

    /**
     * Testa a falha na criação de um banco com código duplicado.
     * O banco de dados rejeita códigos duplicados devido à restrição de unicidade.
     */
    public function test_fail_duplicate_bank_code()
    {
        Bank::factory()->create(['code' => '123456', 'name' => 'Banco 1']);

        $this->expectException(QueryException::class);

        Bank::create([
            'code' => '123456',
            'name' => 'Banco 2',
        ]);
    }
}
