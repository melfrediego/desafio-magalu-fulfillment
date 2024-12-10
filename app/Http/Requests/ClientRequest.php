<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = $this->client ? $this->client->id : 'NULL';

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $clientId,
            'password' => $this->isMethod('POST') ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'phone_whatsapp' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'cpf_cnpj' => 'nullable|string|max:20|unique:users,cpf_cnpj,' . $clientId,
            'person_type' => 'nullable|in:PF,PJ',
        ];
    }

    /**
     * Mensagens de validação personalizadas.
     */
    public function messages(): array
    {
        return [
            // Nome
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais que :max caracteres.',

            // E-mail
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'O e-mail informado não é válido.',
            'email.unique' => 'O e-mail já está em uso por outro usuário.',

            // Senha
            'password.required' => 'O campo senha é obrigatório para novos registros.',
            'password.string' => 'A senha deve ser um texto.',
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',

            // Telefone WhatsApp
            'phone_whatsapp.string' => 'O telefone deve ser um texto.',
            'phone_whatsapp.max' => 'O telefone não pode ter mais que :max caracteres.',

            // Data de nascimento
            'birth_date.date' => 'O campo data de nascimento deve ser uma data válida.',

            // CPF ou CNPJ
            'cpf_cnpj.string' => 'O campo CPF/CNPJ deve ser um texto.',
            'cpf_cnpj.max' => 'O CPF/CNPJ não pode ter mais que :max caracteres.',
            'cpf_cnpj.unique' => 'O CPF/CNPJ já está em uso por outro usuário.',

            // Tipo de pessoa
            'person_type.in' => 'O campo tipo de pessoa deve ser "PF" (Pessoa Física) ou "PJ" (Pessoa Jurídica).',
        ];
    }
}
