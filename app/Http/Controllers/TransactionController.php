<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Jobs\ProcessTransactionJob;
use App\Jobs\ProcessBatchTransactionsJob;
use App\Jobs\ReprocessPendingTransactionsJob;
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
     * Realiza um depósito (sem filas).
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $account = Account::findOrFail($request->account_id);
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
            Log::error("Erro ao realizar depósito: {$e->getMessage()}");

            return response()->json([
                'message' => 'Erro ao realizar depósito.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Realiza um depósito (com filas).
     */
    public function depositAsync(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'account_id' => $request->account_id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        ProcessTransactionJob::dispatch($transaction->id);

        return response()->json([
            'message' => 'Depósito enviado para processamento.',
            'transaction_id' => $transaction->id,
            'status_url' => route('transactions.status', ['id' => $transaction->id]),
        ], 202);
    }

    /**
     * Realiza um saque (sem filas).
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $account = Account::findOrFail($request->account_id);
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
            Log::error("Erro ao realizar saque: {$e->getMessage()}");

            return response()->json([
                'message' => 'Erro ao realizar saque.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Realiza um saque (com filas).
     */
    public function withdrawAsync(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'account_id' => $request->account_id,
            'type' => 'withdraw',
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        ProcessTransactionJob::dispatch($transaction->id);

        return response()->json([
            'message' => 'Saque enviado para processamento.',
            'transaction_id' => $transaction->id,
            'status_url' => route('transactions.status', ['id' => $transaction->id]),
        ], 202);
    }

    /**
     * Realiza uma transferência (sem filas).
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'source_account_id' => 'required|exists:accounts,id',
            'target_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $sourceAccount = Account::findOrFail($request->source_account_id);
            $targetAccount = Account::findOrFail($request->target_account_id);

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
            Log::error("Erro ao realizar transferência: {$e->getMessage()}");

            return response()->json([
                'message' => 'Erro ao realizar transferência.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Realiza uma transferência (com filas).
     */
    public function transferAsync(Request $request)
    {
        $request->validate([
            'source_account_id' => 'required|exists:accounts,id',
            'target_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $transaction = Transaction::create([
            'account_id' => $request->source_account_id,
            'type' => 'transfer',
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        ProcessTransactionJob::dispatch($transaction->id);

        return response()->json([
            'message' => 'Transferência enviada para processamento.',
            'transaction_id' => $transaction->id,
            'status_url' => route('transactions.status', ['id' => $transaction->id]),
        ], 202);
    }

    /**
     * Processa transações em lote (com filas).
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

        $transactionIds = [];

        foreach ($request->transactions as $data) {
            $transaction = Transaction::create([
                'account_id' => $data['account_id'],
                'type' => $data['type'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'status' => 'pending',
            ]);

            $transactionIds[] = $transaction->id;
        }

        ProcessBatchTransactionsJob::dispatch($transactionIds);

        return response()->json([
            'message' => 'Transações em lote enviadas para processamento.',
            'transaction_ids' => $transactionIds,
        ], 202);
    }

    /**
     * Consulta o status de uma transação.
     */
    public function getTransactionStatus($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transação não encontrada.',
            ], 404);
        }

        return response()->json([
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
        ], 200);
    }
}
