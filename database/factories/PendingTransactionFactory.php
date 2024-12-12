<?php

namespace Database\Factories;

use App\Models\PendingTransaction;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendingTransactionFactory extends Factory
{
    protected $model = PendingTransaction::class;

    public function definition()
    {
        return [
            'account_id' => Account::factory(), // Relaciona a uma conta existente
            'target_account_id' => Account::factory(), // Relaciona a uma conta-alvo para transferências
            'amount' => $this->faker->randomFloat(2, 10, 1000), // Valor entre 10 e 1000
            'type' => $this->faker->randomElement(['deposit', 'withdraw', 'transfer']), // Tipo da transação
            'description' => $this->faker->sentence, // Descrição da transação
            'processed' => false, // Pendente por padrão
        ];
    }
}
