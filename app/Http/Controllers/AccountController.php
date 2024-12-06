<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Exception;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Listar todas as contas.
     */
    public function index()
    {
        try {
            $accounts = Account::with(['user', 'bank'])->get();
            return response()->json($accounts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar contas.'], 500);
        }
    }

    /**
     * Criar uma nova conta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'agency' => 'required|unique:accounts,agency|max:10',
            'number' => 'required|unique:accounts,number|max:40',
            'balance' => 'required|numeric|min:0',
            'credit_limit' => 'required',
            'user_id' => 'required',
            'bank_id' => 'required',
        ]);

        try {
            $account = Account::create($request->only('agency', 'number', 'balance', 'credit_limit', 'user_id', 'bank_id'));
            return response()->json($account, 201);
        } catch (Exception $e) {
            dd($e->getMessage());
            return response()->json(['error' => 'Erro ao criar conta.'], 500);
        }
    }

    /**
     * Exibir uma conta específica.
     */
    public function show(Account $account)
    {
        try {
            $account->load(['user', 'bank']); // Carregar relações para exibir mais detalhes
            return response()->json($account, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao exibir conta.'], 500);
        }
    }

    /**
     * Atualizar uma conta.
     */
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'agency' => 'required|unique:accounts,number|max:10',
            'number' => 'sometimes|unique:accounts,number,' . $account->id . '|max:20',
            'balance' => 'sometimes|numeric|min:0',
            'credit_limit' => 'sometimes|numeric|min:0',
            'user_id' => 'sometimes|exists:users,id',
            'bank_id' => 'sometimes|exists:banks,id',
        ]);

        try {
            $account->update($request->only('agency', 'number', 'balance', 'credit_limit', 'user_id', 'bank_id'));
            return response()->json($account, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar conta.'], 500);
        }
    }

    /**
     * Excluir uma conta.
     */
    public function destroy(Account $account)
    {
        try {
            $account->delete();
            return response()->json(['message' => 'Conta excluída com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir conta.'], 500);
        }
    }
}
