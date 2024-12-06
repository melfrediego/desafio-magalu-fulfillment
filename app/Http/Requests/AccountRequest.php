<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AccountRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta solicitação.
     *
     * Este método controla se o usuário tem permissão para executar esta ação.
     * Por padrão, retorna `true`, permitindo acesso para qualquer usuário autorizado.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Manipula falhas de validação.
     *
     * Este método é chamado automaticamente quando os dados enviados falham na validação.
     * Ele lança uma exceção retornando uma resposta JSON contendo os erros de validação
     * e o status HTTP 422 (Unprocessable Entity).
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * Regras de validação para criação e atualização.
     *
     * Define as regras de validação para os campos enviados na requisição.
     * As regras variam dependendo se a requisição é para criação (POST) ou atualização (PUT/PATCH).
     *
     * @return array
     */
    public function rules(): array
    {
        $accountId = $this->account ? $this->account->id : 'NULL'; // Obtém o ID da conta no caso de atualização

        return [
            'agency' => 'required|string|max:10|unique:accounts,agency,' . $accountId,
            'number' => 'required|string|max:40|unique:accounts,number,' . $accountId,
            'balance' => $this->isMethod('POST') // Saldo obrigatório apenas na criação
                ? 'required|numeric|min:0'
                : 'nullable|numeric|min:0',
            'credit_limit' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
            'bank_id' => 'required|exists:banks,id',
        ];
    }

    /**
     * Mensagens de validação personalizadas.
     *
     * Define mensagens específicas para cada regra de validação,
     * facilitando a exibição de mensagens claras e amigáveis ao usuário.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'agency.required' => 'O campo agência é obrigatório.',
            'agency.string' => 'O campo agência deve ser um texto.',
            'agency.max' => 'O campo agência não pode ter mais que 10 caracteres.',
            'agency.unique' => 'A agência já está registrada.',
            'number.required' => 'O campo número é obrigatório.',
            'number.string' => 'O campo número deve ser um texto.',
            'number.max' => 'O campo número não pode ter mais que 40 caracteres.',
            'number.unique' => 'O número da conta já está registrado.',
            'balance.required' => 'O campo saldo é obrigatório para novas contas.',
            'balance.numeric' => 'O saldo deve ser um número.',
            'balance.min' => 'O saldo não pode ser negativo.',
            'credit_limit.required' => 'O campo limite de crédito é obrigatório.',
            'credit_limit.numeric' => 'O limite de crédito deve ser um número.',
            'credit_limit.min' => 'O limite de crédito não pode ser negativo.',
            'user_id.required' => 'O campo usuário é obrigatório.',
            'user_id.exists' => 'O usuário informado não existe.',
            'bank_id.required' => 'O campo banco é obrigatório.',
            'bank_id.exists' => 'O banco informado não existe.',
        ];
    }
}
