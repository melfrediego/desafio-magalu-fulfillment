<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BankRequest extends FormRequest
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
        $bankId = $this->bank ? $this->bank->id : 'NULL'; // Obtém o ID do banco no caso de atualização

        return [
            'code' => [
                'required',
                'min:3',
                'max:6',
                'unique:banks,code,' . $bankId,
            ],
            'name' => 'required|string|max:255|unique:banks,name,' . $bankId,
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
            'code.required' => 'O campo código é obrigatório.',
            'code.min' => 'O código deve de possuir no minino :min caracteres.',
            'code.max' => 'O código deve de possuir no máximo :max caracteres.',
            'code.unique' => 'O código já está registrado.',
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O nome do banco deve ser um texto.',
            'name.max' => 'O nome do banco não pode ultrapassar 255 caracteres.',
            'name.unique' => 'O nome do banco já está registrado.',
        ];
    }
}
