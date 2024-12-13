<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Jobs\ProcessTransactionJob;
use App\Jobs\ProcessBatchTransactionsJob;
use App\Jobs\ReprocessPendingTransactionsJob;
use App\Models\PendingTransaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        DB::beginTransaction();
        try {
            $pendingTransaction = PendingTransaction::create([
                'account_id' => $request->account_id,
                'transaction_id' => uniqid('tx_'),
                'type' => 'deposit',
                'amount' => $request->amount,
                'description' => $request->description,
                'processed' => false,
            ]);

            $transaction = $this->service->processPendingTransaction($pendingTransaction);

            DB::commit();

            return response()->json([
                'message' => 'Depósito realizado com sucesso.',
                'transaction' => $transaction,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
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

        $pendingTransaction = PendingTransaction::create([
            'account_id' => $request->account_id,
            'transaction_id' => uniqid('tx_'),
            'type' => 'deposit',
            'amount' => $request->amount,
            'description' => $request->description,
            'processed' => false,
        ]);

        ProcessTransactionJob::dispatch($pendingTransaction->transaction_id);

        return response()->json([
            'message' => 'Depósito enviado para processamento.',
            'transaction_id' => $pendingTransaction->transaction_id,
            'status_url' => route('transactions.status', ['id' => $pendingTransaction->transaction_id]),
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

        DB::beginTransaction();
        try {
            $pendingTransaction = PendingTransaction::create([
                'account_id' => $request->account_id,
                'transaction_id' => uniqid('tx_'),
                'type' => 'withdraw',
                'amount' => $request->amount,
                'description' => $request->description,
                'processed' => false,
            ]);

            $transaction = $this->service->processPendingTransaction($pendingTransaction);

            DB::commit();

            return response()->json([
                'message' => 'Saque realizado com sucesso.',
                'transaction' => $transaction,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
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

        $pendingTransaction = PendingTransaction::create([
            'account_id' => $request->account_id,
            'transaction_id' => uniqid('tx_'),
            'type' => 'withdraw',
            'amount' => $request->amount,
            'description' => $request->description,
            'processed' => false,
        ]);

        ProcessTransactionJob::dispatch($pendingTransaction->transaction_id);

        return response()->json([
            'message' => 'Saque enviado para processamento.',
            'transaction_id' => $pendingTransaction->transaction_id,
            'status_url' => route('transactions.status', ['id' => $pendingTransaction->transaction_id]),
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

        DB::beginTransaction();
        try {
            $sourceAccount = Account::findOrFail($request->source_account_id);
            $targetAccount = Account::findOrFail($request->target_account_id);

            // Verifica se a conta de origem possui saldo suficiente
            if ($sourceAccount->balance < $request->amount) {
                throw new \Exception('Saldo insuficiente na conta de origem.');
            }

            // Debita o saldo da conta de origem
            $sourceAccount->balance -= $request->amount;
            $sourceAccount->save();

            // Credita o saldo na conta de destino
            $targetAccount->balance += $request->amount;
            $targetAccount->save();

            // Registra a transação pendente
            $pendingTransaction = PendingTransaction::create([
                'account_id' => $request->source_account_id,
                'transaction_id' => uniqid('tx_'),
                'type' => 'transfer',
                'amount' => $request->amount,
                'description' => $request->description,
                'processed' => true, // Marca como processado já que não está usando fila
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transferência realizada com sucesso.',
                'transaction' => $pendingTransaction,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

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

        $pendingTransaction = PendingTransaction::create([
            'account_id' => $request->source_account_id,
            'transaction_id' => uniqid('tx_'),
            'type' => 'transfer',
            'amount' => $request->amount,
            'description' => $request->description,
            'processed' => false,
        ]);

        ProcessTransactionJob::dispatch($pendingTransaction);

        return response()->json([
            'message' => 'Transferência enviada para processamento.',
            'transaction_id' => $pendingTransaction->transaction_id,
            'status_url' => route('transactions.status', ['id' => $pendingTransaction->transaction_id]),
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

        $pendingTransactions = [];

        foreach ($request->transactions as $data) {
            $pendingTransactions[] = PendingTransaction::create([
                'account_id' => $data['account_id'],
                'transaction_id' => uniqid('tx_'),
                'type' => $data['type'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'processed' => false,
            ]);
        }

        ProcessBatchTransactionsJob::dispatch($pendingTransactions);

        return response()->json([
            'message' => 'Transações em lote enviadas para processamento.',
            'transaction_ids' => array_map(fn($transaction) => $transaction->transaction_id, $pendingTransactions),
        ], 202);
    }



    /**
     * Consulta o status de uma transação.
     */
    public function getTransactionStatus($id)
    {
        $transaction = PendingTransaction::where('transaction_id', $id)->first();

        if (!$transaction) {
            return response()->json([
                'message' => 'Transação não encontrada.',
            ], 404);
        }

        return response()->json([
            'transaction_id' => $transaction->transaction_id,
            'status' => $transaction->processed ? 'processed' : 'pending',
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'description' => $transaction->description,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
        ], 200);
    }

    /**
     * Reprocessa transações pendentes (com fila).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reprocessPendingTransactions()
    {
        try {
            ReprocessPendingTransactionsJob::dispatch();

            return response()->json([
                'message' => 'Reprocessamento de transações pendentes enviado para a fila.',
            ], 202);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao disparar o reprocessamento.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
