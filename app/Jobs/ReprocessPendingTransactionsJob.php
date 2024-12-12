<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Jobs\ProcessTransactionJob;
use App\Models\PendingTransaction;
use App\Services\TransactionService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ReprocessPendingTransactionsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Reprocessa todas as transações pendentes.
     */
    public function handle(TransactionService $service)
    {
        $pendingTransactions = PendingTransaction::where('processed', false)->get();

        foreach ($pendingTransactions as $pendingTransaction) {
            try {
                $service->processPendingTransaction($pendingTransaction);
                $pendingTransaction->update(['processed' => true]);
                Log::info("Transação pendente reprocessada com sucesso.", [
                    'transaction_id' => $pendingTransaction->transaction_id,
                ]);
            } catch (Exception $e) {
                Log::error("Erro ao reprocessar transação pendente: {$e->getMessage()}", [
                    'transaction_id' => $pendingTransaction->transaction_id,
                ]);
            }
        }
    }
}
