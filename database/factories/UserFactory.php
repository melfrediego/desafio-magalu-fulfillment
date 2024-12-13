<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define os valores padrão para o modelo User.
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(), // Gera um nome válido
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // Criptografa a senha
            'remember_token' => Str::random(10),
            'is_client' => false, // Padrão para não cliente
            'cpf_cnpj' => $this->faker->numerify('###########'), // Deixado nulo para não cliente
            'person_type' => null, // Deixado nulo para não cliente
        ];
    }

    /**
     * Define o estado do modelo como cliente.
     */
    public function asClient()
    {
        return $this->state(function () {
            return [
                'is_client' => true,
                'cpf_cnpj' => $this->faker->numerify('###########'), // CPF fictício (11 dígitos)
                'person_type' => 'PF', // Pessoa Física
            ];
        });
    }
}
