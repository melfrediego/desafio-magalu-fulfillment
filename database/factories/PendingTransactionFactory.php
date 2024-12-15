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
        $type = $this->faker->randomElement(['deposit', 'withdraw', 'transfer']);

        return [
            'account_id' => Account::factory(), // Relaciona a uma conta existente
            'target_account_id' => $type === 'transfer' ? Account::factory() : null, // Somente para transferências
            'transaction_id' => uniqid('tx_'), // ID único para a transação
            'amount' => $this->faker->randomFloat(2, 10, 1000), // Valor entre 10 e 1000
            'type' => $type, // Tipo da transação
            'description' => $this->faker->sentence, // Descrição da transação
            'processed' => false, // Pendente por padrão
        ];
    }
}

