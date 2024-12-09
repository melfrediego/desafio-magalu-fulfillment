<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Jobs\ProcessTransactionJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReprocessPendingTransactionsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * Reprocessa todas as transações pendentes.
     */
    public function handle()
    {
        $pendingTransactions = Transaction::where('status', 'pending')->get();

        foreach ($pendingTransactions as $pendingTransaction) {
            ProcessTransactionJob::dispatch($pendingTransaction->id);
        }
    }
}
