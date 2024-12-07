<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta solicitação.
     *
     * Este método controla se o usuário tem permissão para executar esta ação.
     * Por padrão, retorna `true`, permitindo acesso para qualquer usuário autorizado.
     */
    public function authorize(): bool
    {
        // dd('true');
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
        $userId = $this->user ? $this->user->id : 'NULL';
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => $this->isMethod('POST') // Verifica se é uma criação (Store)
                ? 'required|string|min:8'
                : 'nullable|string|min:8', // Para Update, a senha é opcional
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
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail fornecido não é válido.',
            'email.unique' => 'O e-mail já está registrado.',
            'password.required' => 'O campo senha é obrigatório para novos usuários.',
            'password.min' => 'A senha deve ter pelo menos :min caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
        ];
    }
}
