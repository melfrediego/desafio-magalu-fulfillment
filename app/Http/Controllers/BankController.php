<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use App\Models\Bank;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    /**
     * Listar todos os bancos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $banks = Bank::orderBy('id', 'DESC')->paginate(10); // Lista com paginação
            return response()->json([
                'status' => true,
                'banks' => $banks
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao listar bancos.'
            ], 404);
        }
    }

    /**
     * Criar um novo banco.
     *
     * @param BankRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BankRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $bank = Bank::create([
                'code' => $request->code,
                'name' => $request->name,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'bank' => $bank,
                'message' => 'Banco cadastrado com sucesso!'
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erro ao criar banco.'
            ], 400);
        }
    }

    /**
     * Exibir um banco específico.
     *
     * @param Bank $bank
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Bank $bank): JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'bank' => $bank
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Banco não encontrado.'
            ], 404);
        }
    }

    /**
     * Atualizar informações de um banco.
     *
     * @param BankRequest $request
     * @param Bank $bank
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BankRequest $request, Bank $bank): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->only(['code', 'name']);

            $bank->update($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'bank' => $bank,
                'message' => 'Banco atualizado com sucesso!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erro ao atualizar banco.'
            ], 400);
        }
    }

    /**
     * Excluir um banco.
     *
     * @param Bank $bank
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Bank $bank): JsonResponse
    {
        try {
            $bank->delete();
            return response()->json([
                'status' => true,
                'message' => 'Banco excluído com sucesso.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao excluir banco.'
            ], 500);
        }
    }
}
