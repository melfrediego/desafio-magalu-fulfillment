<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * O nome do modelo correspondente a esta factory.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define o estado padrão para o modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => \App\Models\Account::factory(),
            'type' => $this->faker->randomElement(['deposit', 'withdraw']),
            'amount' => $this->faker->numberBetween(100, 1000),
            'description' => $this->faker->sentence(),
            'status' => 'success',
        ];
    }
}
