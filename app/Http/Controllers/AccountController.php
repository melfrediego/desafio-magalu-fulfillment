<?php
namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Listar todas as contas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $accounts = Account::with(['user', 'bank'])->orderBy('id', 'DESC')->paginate(10); // Inclui relações
            return response()->json([
                'status' => true,
                'accounts' => $accounts
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao listar contas.'
            ], 404);
        }
    }

    /**
     * Criar uma nova conta.
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AccountRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $account = Account::create([
                'agency' => $request->agency,
                'number' => $request->number,
                'balance' => $request->balance,
                'credit_limit' => $request->credit_limit,
                'user_id' => $request->user_id,
                'bank_id' => $request->bank_id,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'account' => $account,
                'message' => 'Conta cadastrada com sucesso!'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erro ao criar conta.'
            ], 400);
        }
    }

    /**
     * Exibir uma conta específica.
     *
     * @param Account $account
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Account $account): JsonResponse
    {
        try {
            $account->load(['user', 'bank']); // Carregar relações
            return response()->json([
                'status' => true,
                'account' => $account
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Conta não encontrada.'
            ], 404);
        }
    }

    /**
     * Atualizar informações de uma conta.
     *
     * @param AccountRequest $request
     * @param Account $account
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AccountRequest $request, Account $account): JsonResponse
    {
        DB::beginTransaction();

        try {
            $account->update($request->only([
                'agency', 'number', 'balance', 'credit_limit', 'user_id', 'bank_id'
            ]));

            DB::commit();

            return response()->json([
                'status' => true,
                'account' => $account,
                'message' => 'Conta atualizada com sucesso!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erro ao atualizar conta.'
            ], 400);
        }
    }

    /**
     * Excluir uma conta.
     *
     * @param Account $account
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Account $account): JsonResponse
    {
        try {
            $account->delete();
            return response()->json([
                'status' => true,
                'message' => 'Conta excluída com sucesso.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao excluir conta.'
            ], 500);
        }
    }
}
