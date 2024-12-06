<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    /**
     * Realiza um depósito.
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $account = Account::findOrFail($request->account_id);

        try {
            $transaction = $this->service->processTransaction(
                $account,
                'deposit',
                $request->amount,
                $request->description
            );

            return response()->json([
                'message' => 'Depósito realizado com sucesso.',
                'transaction' => $transaction,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erro no depósito: {$e->getMessage()}");

            return response()->json([
                'message' => 'Falha ao realizar depósito.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Realiza um saque.
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $account = Account::findOrFail($request->account_id);

        try {
            $transaction = $this->service->processTransaction(
                $account,
                'withdraw',
                $request->amount,
                $request->description
            );

            return response()->json([
                'message' => 'Saque realizado com sucesso.',
                'transaction' => $transaction,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erro no saque: {$e->getMessage()}");

            return response()->json([
                'message' => 'Falha ao realizar saque.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Realiza uma transferência.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'source_account_id' => 'required|exists:accounts,id',
            'target_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $sourceAccount = Account::findOrFail($request->source_account_id);
        $targetAccount = Account::findOrFail($request->target_account_id);

        try {
            $this->service->transfer(
                $sourceAccount,
                $targetAccount,
                $request->amount,
                $request->description
            );

            return response()->json([
                'message' => 'Transferência realizada com sucesso.',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erro na transferência: {$e->getMessage()}");

            return response()->json([
                'message' => 'Falha ao realizar transferência.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Processa transações em lote.
     */
    public function processBatchTransactions(Request $request)
    {
        $request->validate([
            'transactions' => 'required|array',
            'transactions.*.account_id' => 'required|exists:accounts,id',
            'transactions.*.type' => 'required|in:deposit,withdraw',
            'transactions.*.amount' => 'required|numeric|min:0.01',
            'transactions.*.description' => 'nullable|string|max:255',
        ]);

        try {
            $this->service->processBatchTransactions($request->transactions);

            return response()->json([
                'message' => 'Transações em lote processadas com sucesso.',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Erro no processamento em lote: {$e->getMessage()}");

            return response()->json([
                'message' => 'Falha ao processar transações em lote.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
