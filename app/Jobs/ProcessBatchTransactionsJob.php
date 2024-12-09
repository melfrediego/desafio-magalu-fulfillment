<?php

namespace App\Jobs;

use App\Jobs\ProcessTransactionJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessBatchTransactionsJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    private $transactionIds;

    /**
     * Cria uma nova instância do job.
     *
     * @param array $transactionIds
     */
    public function __construct(array $transactionIds)
    {
        $this->transactionIds = $transactionIds;
    }

    /**
     * Processa transações em lote.
     */
    public function handle()
    {
        foreach ($this->transactionIds as $transactionId) {
            ProcessTransactionJob::dispatch($transactionId);
        }
    }
}
