<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Listar todos os bancos.
     */
    public function index()
    {
        try {
            $banks = Bank::all();
            return response()->json($banks, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao listar bancos.'], 500);
        }
    }

    /**
     * Criar um novo banco.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:banks,code|min:3|max:5',
            'name' => 'required|max:120',
        ]);

        try {
            $bank = Bank::create($request->only('code', 'name'));
            return response()->json($bank, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar banco.'], 500);
        }
    }

    /**
     * Exibir um banco específico.
     */
    public function show(Bank $bank)
    {
        try {
            return response()->json($bank, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao exibir banco.'], 500);
        }
    }

    /**
     * Atualizar informações de um banco.
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'code' => 'sometimes|unique:banks,code,' . $bank->id . '|max:10',
            'name' => 'sometimes|max:255',
        ]);

        try {
            $bank->update($request->only('code', 'name'));
            return response()->json($bank, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar banco.'], 500);
        }
    }

    /**
     * Excluir um banco.
     */
    public function destroy(Bank $bank)
    {
        try {
            $bank->delete();
            return response()->json(['message' => 'Banco excluído com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao excluir banco.'], 500);
        }
    }
}
