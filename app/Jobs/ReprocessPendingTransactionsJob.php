<?php

namespace App\Jobs;

use App\Models\PendingTransaction;
use App\Services\TransactionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ReprocessPendingTransactionsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Processa transações pendentes.
     *
     * @param TransactionService $service
     */
    public function handle(TransactionService $service)
    {
        $batchSize = 50; // Define o tamanho do lote para processamento
        $page = 1;

        do {
            $pendingTransactions = PendingTransaction::where('processed', false)
                ->orderBy('id')
                ->limit($batchSize)
                ->offset(($page - 1) * $batchSize)
                ->get();

            foreach ($pendingTransactions as $pendingTransaction) {
                try {
                    $service->processPendingTransaction($pendingTransaction);
                    $pendingTransaction->update(['processed' => true]);

                    Log::info("Transação pendente reprocessada com sucesso.", [
                        'transaction_id' => $pendingTransaction->transaction_id,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Erro ao reprocessar transação pendente: {$e->getMessage()}", [
                        'transaction_id' => $pendingTransaction->transaction_id,
                    ]);
                }
            }

            $page++;
        } while ($pendingTransactions->isNotEmpty());
    }
}
