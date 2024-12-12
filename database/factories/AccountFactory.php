<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = \App\Models\Account::class;

    public function definition()
    {
        return [
            'agency' => $this->faker->regexify('[0-9]{4}'), // Agência com 4 dígitos
            'number' => $this->faker->regexify('[0-9]{5}'), // Número da conta com 5 dígitos
            'balance' => $this->faker->randomFloat(2, 0, 10000), // Saldo entre 0 e 10.000
            'credit_limit' => $this->faker->randomFloat(2, 0, 5000), // Limite de crédito entre 0 e 5.000
            'user_id' => User::factory(), // Relacionamento com usuário
            'bank_id' => Bank::factory(), // Relacionamento com banco
        ];
    }
}
